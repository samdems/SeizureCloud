@props([
    'action' => '',
    'method' => 'POST',
    'seizure' => null,
    'users' => collect(),
    'showUserSelection' => false,
    'showTimeFields' => true,
    'timeFieldsEditable' => true,
    'hideTimeFieldsInitially' => false,
    'hideSubmitInitially' => false,
    'formId' => 'seizure_form',
    'submitText' => 'Save Seizure Record',
    'cancelUrl' => null
])

@php
    $isEdit = isset($seizure);
    $defaultSeverity = old('severity', $seizure?->severity ?? 5);
    $selectedUserId = old('user_id', $seizure?->user_id ?? auth()->id());
@endphp

<form id="{{ $formId }}" action="{{ $action }}" method="{{ $method }}" class="space-y-6">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <!-- Hidden user_id field -->
    <input type="hidden" id="form_user_id" name="user_id" value="{{ $selectedUserId }}">

    <!-- Hidden time fields for form submission -->
    @if(!$timeFieldsEditable)
        <input type="hidden" id="form_start_time" name="start_time">
        <input type="hidden" id="form_end_time" name="end_time">
        <input type="hidden" id="form_duration_minutes" name="duration_minutes">
    @endif

    <!-- User Selection (if enabled) -->
    @if($showUserSelection && $users->count() > 0)
        <div class="form-control">
            <label class="label">
                <span class="label-text font-semibold">User</span>
            </label>
            <select id="user_select" name="user_id" class="select select-bordered w-full">
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $user->id == $selectedUserId ? 'selected' : '' }}>
                        {{ $user->name }}{{ $user->id === auth()->id() ? ' (You)' : ' - Trusted Access' }}
                    </option>
                @endforeach
            </select>
            @error('user_id')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
        </div>
    @endif

    <!-- Time Information -->
    @if($showTimeFields)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="time-fields-container" style="{{ $hideTimeFieldsInitially ? 'display: none;' : '' }}">
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-semibold">Start Time *</span>
                    @if($timeFieldsEditable)
                        <span class="label-text-alt text-primary cursor-pointer" onclick="setStartTimeToNow(event)">Set to now</span>
                    @endif
                </label>
                @if($timeFieldsEditable)
                    <input type="datetime-local" id="start_time_input" name="start_time"
                           value="{{ old('start_time', $seizure?->start_time?->format('Y-m-d\TH:i')) }}"
                           class="input input-bordered" onchange="updateDuration()" required>
                @else
                    <div id="display_start_time" class="input input-bordered bg-base-200 flex items-center"></div>
                @endif
                @error('start_time')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text font-semibold">End Time</span>
                    @if($timeFieldsEditable)
                        <span class="label-text-alt text-primary cursor-pointer" onclick="setEndTimeToNow(event)">Set to now</span>
                    @endif
                </label>
                @if($timeFieldsEditable)
                    <input type="datetime-local" id="end_time_input" name="end_time"
                           value="{{ old('end_time', $seizure?->end_time?->format('Y-m-d\TH:i')) }}"
                           class="input input-bordered" onchange="updateDuration()">
                @else
                    <div id="display_end_time" class="input input-bordered bg-base-200 flex items-center"></div>
                @endif
                @error('end_time')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text font-semibold">Duration (minutes)</span>
                </label>
                @if($timeFieldsEditable)
                    <input type="number" id="duration_input" name="duration_minutes" min="0"
                           value="{{ old('duration_minutes', $seizure?->duration_minutes) }}"
                           class="input input-bordered" placeholder="Auto-calculated" onchange="updateEndTime()">
                @else
                    <div id="display_duration" class="input input-bordered bg-base-200 flex items-center"></div>
                @endif
                @error('duration_minutes')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
            </div>
        </div>
    @endif

    <!-- Severity Rating with Circles -->
    <div class="form-control">
        <label class="label">
            <span class="label-text font-semibold">Severity (1-10) *</span>
            <span class="label-text-alt">Click circles to rate severity</span>
        </label>

        <input type="hidden" id="severity_input" name="severity" value="{{ $defaultSeverity }}" required>

        <div class="flex flex-wrap gap-2 justify-center py-4">
            @for($i = 1; $i <= 10; $i++)
                <button type="button" class="severity-btn transition-all hover:opacity-80 flex-shrink-0 flex flex-col items-center gap-1"
                        data-value="{{ $i }}" onclick="setSeverity({{ $i }})">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle class="severity-circle" cx="12" cy="12" r="10"
                                stroke="currentColor" stroke-width="2" fill="none"/>
                    </svg>
                    <span class="text-xs font-semibold">{{ $i }}</span>
                </button>
            @endfor
        </div>

        <!-- Severity Description -->
        <div class="text-center mt-2">
            <div class="text-sm opacity-70">
                <span id="severity_description">Moderate severity</span>
                <span class="mx-2">•</span>
                <span id="severity_color_indicator" class="font-semibold">Rating: {{ $defaultSeverity }}</span>
            </div>
        </div>

        @error('severity')
            <label class="label">
                <span class="label-text-alt text-error">{{ $message }}</span>
            </label>
        @enderror
    </div>

    <!-- Seizure Type -->
    <div class="form-control">
        <label class="label">
            <span class="label-text font-semibold">Type of Seizure</span>
        </label>
        <select name="seizure_type" class="select select-bordered">
            <option value="">Select seizure type</option>
            <option value="focal_aware" {{ old('seizure_type', $seizure?->seizure_type) === 'focal_aware' ? 'selected' : '' }}>Focal Aware (Simple Partial)</option>
            <option value="focal_impaired" {{ old('seizure_type', $seizure?->seizure_type) === 'focal_impaired' ? 'selected' : '' }}>Focal Impaired Awareness (Complex Partial)</option>
            <option value="focal_motor" {{ old('seizure_type', $seizure?->seizure_type) === 'focal_motor' ? 'selected' : '' }}>Focal Motor</option>
            <option value="focal_non_motor" {{ old('seizure_type', $seizure?->seizure_type) === 'focal_non_motor' ? 'selected' : '' }}>Focal Non-Motor</option>
            <option value="generalized_tonic_clonic" {{ old('seizure_type', $seizure?->seizure_type) === 'generalized_tonic_clonic' ? 'selected' : '' }}>Generalized Tonic-Clonic</option>
            <option value="absence" {{ old('seizure_type', $seizure?->seizure_type) === 'absence' ? 'selected' : '' }}>Absence</option>
            <option value="myoclonic" {{ old('seizure_type', $seizure?->seizure_type) === 'myoclonic' ? 'selected' : '' }}>Myoclonic</option>
            <option value="atonic" {{ old('seizure_type', $seizure?->seizure_type) === 'atonic' ? 'selected' : '' }}>Atonic (Drop Attack)</option>
            <option value="tonic" {{ old('seizure_type', $seizure?->seizure_type) === 'tonic' ? 'selected' : '' }}>Tonic</option>
            <option value="clonic" {{ old('seizure_type', $seizure?->seizure_type) === 'clonic' ? 'selected' : '' }}>Clonic</option>
            <option value="unknown" {{ old('seizure_type', $seizure?->seizure_type) === 'unknown' ? 'selected' : '' }}>Unknown/Uncertain</option>
        </select>
        @error('seizure_type')
            <label class="label">
                <span class="label-text-alt text-error">{{ $message }}</span>
            </label>
        @enderror
    </div>

    <!-- Video Evidence -->
    <div class="form-control">
        <label class="label">
            <span class="label-text font-semibold">Video Evidence</span>
        </label>
        <div class="space-y-2">
            <label class="cursor-pointer label justify-start gap-2">
                <input type="checkbox" name="has_video_evidence" value="1"
                       {{ old('has_video_evidence', $seizure?->has_video_evidence) ? 'checked' : '' }}
                       class="checkbox checkbox-primary">
                <span class="label-text">Video recording of seizure available</span>
            </label>
            <div class="form-control">
                <textarea name="video_notes" rows="2" class="textarea textarea-bordered textarea-sm"
                          placeholder="Notes about video evidence (location, who recorded, etc.)">{{ old('video_notes', $seizure?->video_notes) }}</textarea>
            </div>
        </div>
        @error('has_video_evidence')
            <label class="label">
                <span class="label-text-alt text-error">{{ $message }}</span>
            </label>
        @enderror
        @error('video_notes')
            <label class="label">
                <span class="label-text-alt text-error">{{ $message }}</span>
            </label>
        @enderror
    </div>

    <!-- Possible Triggers -->
    <div class="form-control">
        <label class="label">
            <span class="label-text font-semibold">Possible Triggers</span>
            <span class="label-text-alt">Select all that may have contributed</span>
        </label>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
            <label class="cursor-pointer label justify-start gap-2">
                <input type="checkbox" name="triggers[]" value="stress"
                       {{ in_array('stress', old('triggers', $seizure?->triggers ?? [])) ? 'checked' : '' }}
                       class="checkbox checkbox-primary checkbox-sm">
                <span class="label-text text-sm">Stress</span>
            </label>
            <label class="cursor-pointer label justify-start gap-2">
                <input type="checkbox" name="triggers[]" value="lack_of_sleep"
                       {{ in_array('lack_of_sleep', old('triggers', $seizure?->triggers ?? [])) ? 'checked' : '' }}
                       class="checkbox checkbox-primary checkbox-sm">
                <span class="label-text text-sm">Lack of sleep</span>
            </label>
            <label class="cursor-pointer label justify-start gap-2">
                <input type="checkbox" name="triggers[]" value="missed_medication"
                       {{ in_array('missed_medication', old('triggers', $seizure?->triggers ?? [])) ? 'checked' : '' }}
                       class="checkbox checkbox-primary checkbox-sm">
                <span class="label-text text-sm">Missed medication</span>
            </label>
            <label class="cursor-pointer label justify-start gap-2">
                <input type="checkbox" name="triggers[]" value="illness"
                       {{ in_array('illness', old('triggers', $seizure?->triggers ?? [])) ? 'checked' : '' }}
                       class="checkbox checkbox-primary checkbox-sm">
                <span class="label-text text-sm">Illness/fever</span>
            </label>
            <label class="cursor-pointer label justify-start gap-2">
                <input type="checkbox" name="triggers[]" value="alcohol"
                       {{ in_array('alcohol', old('triggers', $seizure?->triggers ?? [])) ? 'checked' : '' }}
                       class="checkbox checkbox-primary checkbox-sm">
                <span class="label-text text-sm">Alcohol</span>
            </label>
            <label class="cursor-pointer label justify-start gap-2">
                <input type="checkbox" name="triggers[]" value="flashing_lights"
                       {{ in_array('flashing_lights', old('triggers', $seizure?->triggers ?? [])) ? 'checked' : '' }}
                       class="checkbox checkbox-primary checkbox-sm">
                <span class="label-text text-sm">Flashing lights</span>
            </label>
            <label class="cursor-pointer label justify-start gap-2">
                <input type="checkbox" name="triggers[]" value="hormonal"
                       {{ in_array('hormonal', old('triggers', $seizure?->triggers ?? [])) ? 'checked' : '' }}
                       class="checkbox checkbox-primary checkbox-sm">
                <span class="label-text text-sm">Hormonal changes</span>
            </label>
            <label class="cursor-pointer label justify-start gap-2">
                <input type="checkbox" name="triggers[]" value="dehydration"
                       {{ in_array('dehydration', old('triggers', $seizure?->triggers ?? [])) ? 'checked' : '' }}
                       class="checkbox checkbox-primary checkbox-sm">
                <span class="label-text text-sm">Dehydration</span>
            </label>
            <label class="cursor-pointer label justify-start gap-2">
                <input type="checkbox" name="triggers[]" value="low_blood_sugar"
                       {{ in_array('low_blood_sugar', old('triggers', $seizure?->triggers ?? [])) ? 'checked' : '' }}
                       class="checkbox checkbox-primary checkbox-sm">
                <span class="label-text text-sm">Low blood sugar</span>
            </label>
        </div>
        <div class="form-control mt-3">
            <textarea name="other_triggers" rows="2" class="textarea textarea-bordered textarea-sm"
                      placeholder="Other triggers not listed above">{{ old('other_triggers', $seizure?->other_triggers) }}</textarea>
        </div>
        @error('triggers')
            <label class="label">
                <span class="label-text-alt text-error">{{ $message }}</span>
            </label>
        @enderror
        @error('other_triggers')
            <label class="label">
                <span class="label-text-alt text-error">{{ $message }}</span>
            </label>
        @enderror
    </div>

    <!-- Pre-ictal Symptoms -->
    <div class="form-control">
        <label class="label">
            <span class="label-text font-semibold">Pre-ictal Symptoms</span>
            <span class="label-text-alt">What did you notice before the seizure?</span>
        </label>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <label class="cursor-pointer label justify-start gap-2">
                <input type="checkbox" name="pre_ictal_symptoms[]" value="aura"
                       {{ in_array('aura', old('pre_ictal_symptoms', $seizure?->pre_ictal_symptoms ?? [])) ? 'checked' : '' }}
                       class="checkbox checkbox-primary checkbox-sm">
                <span class="label-text text-sm">Aura/warning feeling</span>
            </label>
            <label class="cursor-pointer label justify-start gap-2">
                <input type="checkbox" name="pre_ictal_symptoms[]" value="mood_change"
                       {{ in_array('mood_change', old('pre_ictal_symptoms', $seizure?->pre_ictal_symptoms ?? [])) ? 'checked' : '' }}
                       class="checkbox checkbox-primary checkbox-sm">
                <span class="label-text text-sm">Mood changes</span>
            </label>
            <label class="cursor-pointer label justify-start gap-2">
                <input type="checkbox" name="pre_ictal_symptoms[]" value="headache"
                       {{ in_array('headache', old('pre_ictal_symptoms', $seizure?->pre_ictal_symptoms ?? [])) ? 'checked' : '' }}
                       class="checkbox checkbox-primary checkbox-sm">
                <span class="label-text text-sm">Headache</span>
            </label>
            <label class="cursor-pointer label justify-start gap-2">
                <input type="checkbox" name="pre_ictal_symptoms[]" value="confusion"
                       {{ in_array('confusion', old('pre_ictal_symptoms', $seizure?->pre_ictal_symptoms ?? [])) ? 'checked' : '' }}
                       class="checkbox checkbox-primary checkbox-sm">
                <span class="label-text text-sm">Confusion</span>
            </label>
            <label class="cursor-pointer label justify-start gap-2">
                <input type="checkbox" name="pre_ictal_symptoms[]" value="unusual_sensations"
                       {{ in_array('unusual_sensations', old('pre_ictal_symptoms', $seizure?->pre_ictal_symptoms ?? [])) ? 'checked' : '' }}
                       class="checkbox checkbox-primary checkbox-sm">
                <span class="label-text text-sm">Unusual sensations</span>
            </label>
            <label class="cursor-pointer label justify-start gap-2">
                <input type="checkbox" name="pre_ictal_symptoms[]" value="none_noticed"
                       {{ in_array('none_noticed', old('pre_ictal_symptoms', $seizure?->pre_ictal_symptoms ?? [])) ? 'checked' : '' }}
                       class="checkbox checkbox-primary checkbox-sm">
                <span class="label-text text-sm">No warning signs</span>
            </label>
        </div>
        <div class="form-control mt-3">
            <textarea name="pre_ictal_notes" rows="2" class="textarea textarea-bordered textarea-sm"
                      placeholder="Describe any pre-ictal symptoms in detail">{{ old('pre_ictal_notes', $seizure?->pre_ictal_notes) }}</textarea>
        </div>
        @error('pre_ictal_symptoms')
            <label class="label">
                <span class="label-text-alt text-error">{{ $message }}</span>
            </label>
        @enderror
        @error('pre_ictal_notes')
            <label class="label">
                <span class="label-text-alt text-error">{{ $message }}</span>
            </label>
        @enderror
    </div>

    <!-- Post-ictal Recovery -->
    <div class="form-control">
        <label class="label">
            <span class="label-text font-semibold">Post-ictal Recovery</span>
            <span class="label-text-alt">How did you feel after the seizure?</span>
        </label>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="label">
                    <span class="label-text text-sm">Recovery time</span>
                </label>
                <select name="recovery_time" class="select select-bordered select-sm">
                    <option value="">Select recovery time</option>
                    <option value="immediate" {{ old('recovery_time', $seizure?->recovery_time) === 'immediate' ? 'selected' : '' }}>Immediate (< 5 minutes)</option>
                    <option value="short" {{ old('recovery_time', $seizure?->recovery_time) === 'short' ? 'selected' : '' }}>Short (5-30 minutes)</option>
                    <option value="moderate" {{ old('recovery_time', $seizure?->recovery_time) === 'moderate' ? 'selected' : '' }}>Moderate (30min - 2 hours)</option>
                    <option value="long" {{ old('recovery_time', $seizure?->recovery_time) === 'long' ? 'selected' : '' }}>Long (2-6 hours)</option>
                    <option value="very_long" {{ old('recovery_time', $seizure?->recovery_time) === 'very_long' ? 'selected' : '' }}>Very long (6+ hours)</option>
                </select>
            </div>
            <div class="space-y-2">
                <label class="cursor-pointer label justify-start gap-2">
                    <input type="checkbox" name="slept_after" value="1"
                           {{ old('slept_after', $seizure?->slept_after) ? 'checked' : '' }}
                           class="checkbox checkbox-primary checkbox-sm">
                    <span class="label-text text-sm">Slept after seizure</span>
                </label>
                <label class="cursor-pointer label justify-start gap-2">
                    <input type="checkbox" name="post_ictal_confusion" value="1"
                           {{ old('post_ictal_confusion', $seizure?->post_ictal_confusion) ? 'checked' : '' }}
                           class="checkbox checkbox-primary checkbox-sm">
                    <span class="label-text text-sm">Post-ictal confusion</span>
                </label>
                <label class="cursor-pointer label justify-start gap-2">
                    <input type="checkbox" name="post_ictal_headache" value="1"
                           {{ old('post_ictal_headache', $seizure?->post_ictal_headache) ? 'checked' : '' }}
                           class="checkbox checkbox-primary checkbox-sm">
                    <span class="label-text text-sm">Post-ictal headache</span>
                </label>
            </div>
        </div>
        <div class="form-control mt-3">
            <textarea name="recovery_notes" rows="2" class="textarea textarea-bordered textarea-sm"
                      placeholder="Describe your recovery experience">{{ old('recovery_notes', $seizure?->recovery_notes) }}</textarea>
        </div>
        @error('recovery_time')
            <label class="label">
                <span class="label-text-alt text-error">{{ $message }}</span>
            </label>
        @enderror
        @error('recovery_notes')
            <label class="label">
                <span class="label-text-alt text-error">{{ $message }}</span>
            </label>
        @enderror
    </div>

    <!-- Medical Information -->
    <div class="divider">Medical Information</div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="form-control">
            <label class="label">
                <span class="label-text font-semibold">Period & Hormones</span>
            </label>
            <div class="space-y-2">
                <label class="cursor-pointer label justify-start gap-2">
                    <input type="checkbox" name="on_period" value="1"
                           {{ old('on_period', $seizure?->on_period) ? 'checked' : '' }}
                           class="checkbox checkbox-primary">
                    <span class="label-text">On period</span>
                </label>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text text-sm">Days since last period started</span>
                    </label>
                    <input type="number" name="days_since_period" min="0" max="100"
                           value="{{ old('days_since_period', $seizure?->days_since_period) }}"
                           class="input input-bordered input-sm" placeholder="Optional">
                </div>
            </div>
        </div>

        <div class="form-control">
            <label class="label">
                <span class="label-text font-semibold">Emergency Response</span>
            </label>
            <div class="space-y-2">
                <label class="cursor-pointer label justify-start gap-2">
                    <input type="checkbox" name="ambulance_called" value="1"
                           {{ old('ambulance_called', $seizure?->ambulance_called) ? 'checked' : '' }}
                           class="checkbox checkbox-primary">
                    <span class="label-text">Ambulance called</span>
                </label>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text text-sm">NHS Contact</span>
                    </label>
                    <select name="nhs_contact_type" class="select select-bordered select-sm">
                        <option value="">No contact</option>
                        <option value="gp" {{ old('nhs_contact_type', $seizure?->nhs_contact_type) === 'gp' ? 'selected' : '' }}>GP</option>
                        <option value="hospital" {{ old('nhs_contact_type', $seizure?->nhs_contact_type) === 'hospital' ? 'selected' : '' }}>Hospital</option>
                        <option value="111" {{ old('nhs_contact_type', $seizure?->nhs_contact_type) === '111' ? 'selected' : '' }}>NHS 111</option>
                        <option value="999" {{ old('nhs_contact_type', $seizure?->nhs_contact_type) === '999' ? 'selected' : '' }}>Emergency (999)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Medication Adherence -->
    <div class="form-control">
        <label class="label">
            <span class="label-text-alt">How well did you take your epilepsy medicines recently?</span>
        </label>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="label">
                    <span class="label-text text-sm">Medication adherence (last 7 days)</span>
                </label>
                <select name="medication_adherence" class="select select-bordered select-sm">
                    <option value="">Select adherence level</option>
                    <option value="excellent" {{ old('medication_adherence', $seizure?->medication_adherence) === 'excellent' ? 'selected' : '' }}>Excellent (100% of doses)</option>
                    <option value="good" {{ old('medication_adherence', $seizure?->medication_adherence) === 'good' ? 'selected' : '' }}>Good (75-99% of doses)</option>
                    <option value="fair" {{ old('medication_adherence', $seizure?->medication_adherence) === 'fair' ? 'selected' : '' }}>Fair (50-74% of doses)</option>
                    <option value="poor" {{ old('medication_adherence', $seizure?->medication_adherence) === 'poor' ? 'selected' : '' }}>Poor (< 50% of doses)</option>
                </select>
            </div>
            <div class="space-y-2">
                <label class="cursor-pointer label justify-start gap-2">
                    <input type="checkbox" name="recent_medication_change" value="1"
                           {{ old('recent_medication_change', $seizure?->recent_medication_change) ? 'checked' : '' }}
                           class="checkbox checkbox-primary checkbox-sm">
                    <span class="label-text text-sm">Recent medication change</span>
                </label>
                <label class="cursor-pointer label justify-start gap-2">
                    <input type="checkbox" name="experiencing_side_effects" value="1"
                           {{ old('experiencing_side_effects', $seizure?->experiencing_side_effects) ? 'checked' : '' }}
                           class="checkbox checkbox-primary checkbox-sm">
                    <span class="label-text text-sm">Experiencing side effects</span>
                </label>
            </div>
        </div>
        <div class="form-control mt-3">
            <textarea name="medication_notes" rows="2" class="textarea textarea-bordered textarea-sm"
                      placeholder="Notes about medications, changes, or side effects">{{ old('medication_notes', $seizure?->medication_notes) }}</textarea>
        </div>
        @error('medication_adherence')
            <label class="label">
                <span class="label-text-alt text-error">{{ $message }}</span>
            </label>
        @enderror
        @error('medication_notes')
            <label class="label">
                <span class="label-text-alt text-error">{{ $message }}</span>
            </label>
        @enderror
    </div>

    <!-- General Wellbeing -->
    <div class="form-control">
        <label class="label">
            <span class="label-text font-semibold">General Wellbeing</span>
            <span class="label-text-alt">How have you been feeling overall?</span>
        </label>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="label">
                    <span class="label-text text-sm">Overall wellbeing (last week)</span>
                </label>
                <select name="wellbeing_rating" class="select select-bordered select-sm">
                    <option value="">Rate your wellbeing</option>
                    <option value="excellent" {{ old('wellbeing_rating', $seizure?->wellbeing_rating) === 'excellent' ? 'selected' : '' }}>Excellent</option>
                    <option value="good" {{ old('wellbeing_rating', $seizure?->wellbeing_rating) === 'good' ? 'selected' : '' }}>Good</option>
                    <option value="fair" {{ old('wellbeing_rating', $seizure?->wellbeing_rating) === 'fair' ? 'selected' : '' }}>Fair</option>
                    <option value="poor" {{ old('wellbeing_rating', $seizure?->wellbeing_rating) === 'poor' ? 'selected' : '' }}>Poor</option>
                    <option value="very_poor" {{ old('wellbeing_rating', $seizure?->wellbeing_rating) === 'very_poor' ? 'selected' : '' }}>Very poor</option>
                </select>
            </div>
            <div>
                <label class="label">
                    <span class="label-text text-sm">Sleep quality (last week)</span>
                </label>
                <select name="sleep_quality" class="select select-bordered select-sm">
                    <option value="">Rate your sleep</option>
                    <option value="excellent" {{ old('sleep_quality', $seizure?->sleep_quality) === 'excellent' ? 'selected' : '' }}>Excellent</option>
                    <option value="good" {{ old('sleep_quality', $seizure?->sleep_quality) === 'good' ? 'selected' : '' }}>Good</option>
                    <option value="fair" {{ old('sleep_quality', $seizure?->sleep_quality) === 'fair' ? 'selected' : '' }}>Fair</option>
                    <option value="poor" {{ old('sleep_quality', $seizure?->sleep_quality) === 'poor' ? 'selected' : '' }}>Poor</option>
                    <option value="very_poor" {{ old('sleep_quality', $seizure?->sleep_quality) === 'very_poor' ? 'selected' : '' }}>Very poor</option>
                </select>
            </div>
        </div>
        <div class="form-control mt-3">
            <textarea name="wellbeing_notes" rows="2" class="textarea textarea-bordered textarea-sm"
                      placeholder="Any other observations about your health, mood, or wellbeing">{{ old('wellbeing_notes', $seizure?->wellbeing_notes) }}</textarea>
        </div>
        @error('wellbeing_rating')
            <label class="label">
                <span class="label-text-alt text-error">{{ $message }}</span>
            </label>
        @enderror
        @error('sleep_quality')
            <label class="label">
                <span class="label-text-alt text-error">{{ $message }}</span>
            </label>
        @enderror
        @error('wellbeing_notes')
            <label class="label">
                <span class="label-text-alt text-error">{{ $message }}</span>
            </label>
        @enderror
    </div>

    <!-- Postictal State End -->
    <div class="form-control">
        <label class="label">
            <span class="label-text font-semibold">Postictal state ended at</span>
            <button type="button" onclick="setPostictalToNow(event)" class="btn btn-xs btn-primary" data-no-loading>
                Set to now
            </button>
        </label>
        <input type="datetime-local" name="postictal_state_end" id="postictal_state_end"
               value="{{ old('postictal_state_end', $seizure?->postictal_state_end?->format('Y-m-d\TH:i')) }}"
               class="input input-bordered">
        @error('postictal_state_end')
            <label class="label">
                <span class="label-text-alt text-error">{{ $message }}</span>
            </label>
        @enderror
    </div>

    <!-- Notes -->
    <div class="form-control">
        <label class="label">
            <span class="label-text font-semibold">Notes</span>
            <button type="button" onclick="openNotesModal()" class="btn btn-xs btn-outline">
                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                </svg>
                Expand
            </button>
        </label>
        <textarea name="notes" id="notes" rows="4" class="textarea textarea-bordered"
                  placeholder="Optional notes about the seizure...">{{ old('notes', $seizure?->notes) }}</textarea>
        @error('notes')
            <label class="label">
                <span class="label-text-alt text-error">{{ $message }}</span>
            </label>
        @enderror
    </div>

    <!-- Form Actions -->
    <div class="flex justify-between">
        @if($cancelUrl)
            <a href="{{ $cancelUrl }}" class="btn btn-outline">Cancel</a>
        @else
            <button type="button" onclick="cancelForm()" class="btn btn-outline">Cancel</button>
        @endif
        <button type="submit" class="btn btn-primary" id="submit-button" style="{{ $hideSubmitInitially ? 'display: none;' : '' }}" {{ $hideSubmitInitially ? 'disabled' : '' }}>{{ $submitText }}</button>
    </div>
</form>

<!-- Notes Modal -->
<dialog id="notes_modal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg mb-4">Seizure Notes</h3>
        <textarea id="fullscreen_notes" rows="10" class="textarea textarea-bordered w-full"
                  placeholder="Enter detailed notes about the seizure..."></textarea>
        <div class="modal-action">
            <button type="button" onclick="saveNotes()" class="btn btn-primary">Save</button>
            <button type="button" onclick="closeNotesModal()" class="btn">Cancel</button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<script>
// Severity rating system with circles
function setSeverity(value) {
    document.getElementById('severity_input').value = value;
    updateSeverityDisplay(value);
}

function updateSeverityDisplay(selectedValue) {
    const buttons = document.querySelectorAll('.severity-btn');
    const description = document.getElementById('severity_description');
    const colorIndicator = document.getElementById('severity_color_indicator');

    buttons.forEach((btn, index) => {
        const value = index + 1;
        const circle = btn.querySelector('.severity-circle');

        if (value <= selectedValue) {
            // Filled state
            circle.setAttribute('fill', getColorForSeverity(selectedValue));
            circle.setAttribute('stroke', getColorForSeverity(selectedValue));
            btn.classList.add('scale-110');
        } else {
            // Empty state
            circle.setAttribute('fill', 'none');
            circle.setAttribute('stroke', 'currentColor');
            btn.classList.remove('scale-110');
        }
    });

    // Update description and color indicator
    if (description && colorIndicator) {
        if (selectedValue <= 2) {
            description.textContent = 'Very mild severity';
            colorIndicator.className = 'font-semibold text-success';
        } else if (selectedValue <= 4) {
            description.textContent = 'Mild to moderate severity';
            colorIndicator.className = 'font-semibold text-info';
        } else if (selectedValue <= 6) {
            description.textContent = 'Moderate severity';
            colorIndicator.className = 'font-semibold text-warning';
        } else if (selectedValue <= 8) {
            description.textContent = 'Severe seizure';
            colorIndicator.className = 'font-semibold text-error';
        } else {
            description.textContent = 'Very severe seizure';
            colorIndicator.className = 'font-semibold text-error font-bold';
        }
        colorIndicator.textContent = `Rating: ${selectedValue}`;
    }
}

function getColorForSeverity(value) {
    if (value <= 2) return '#10b981'; // green
    if (value <= 4) return '#3b82f6'; // blue
    if (value <= 6) return '#f59e0b'; // yellow/orange
    if (value <= 8) return '#f97316'; // orange
    return '#ef4444'; // red
}

// Time manipulation functions (if editable)
@if($timeFieldsEditable)
function updateHiddenFields() {
    const startInput = document.getElementById('start_time_input');
    const endInput = document.getElementById('end_time_input');
    const durationInput = document.getElementById('duration_input');

    if (document.getElementById('form_start_time')) {
        document.getElementById('form_start_time').value = startInput.value;
        document.getElementById('form_end_time').value = endInput.value;
        document.getElementById('form_duration_minutes').value = durationInput.value;
    }
}

function updateDuration() {
    const startInput = document.getElementById('start_time_input');
    const endInput = document.getElementById('end_time_input');
    const durationInput = document.getElementById('duration_input');

    if (startInput.value && endInput.value) {
        const start = new Date(startInput.value);
        const end = new Date(endInput.value);
        const diffMinutes = Math.max(0, Math.floor((end - start) / 60000));
        durationInput.value = diffMinutes;
        if (typeof updateHiddenFields === 'function') updateHiddenFields();
    }
}

function updateEndTime() {
    const startInput = document.getElementById('start_time_input');
    const endInput = document.getElementById('end_time_input');
    const durationInput = document.getElementById('duration_input');

    if (startInput.value && durationInput.value) {
        const start = new Date(startInput.value);
        const end = new Date(start.getTime() + (parseInt(durationInput.value) * 60000));

        const offset = end.getTimezoneOffset() * 60000;
        const localDate = new Date(end.getTime() - offset);
        endInput.value = localDate.toISOString().slice(0, 16);
        if (typeof updateHiddenFields === 'function') updateHiddenFields();
    }
}

function setStartTimeToNow(event) {
    const now = new Date();
    const offset = now.getTimezoneOffset() * 60000;
    const localDate = new Date(now.getTime() - offset);
    document.getElementById('start_time_input').value = localDate.toISOString().slice(0, 16);
    updateDuration();

    // Clear any accidentally applied loading state
    const element = event && event.target;
    if (element && element.classList.contains('loading')) {
        element.classList.remove('loading');
    }
}

function setEndTimeToNow(event) {
    const now = new Date();
    const offset = now.getTimezoneOffset() * 60000;
    const localDate = new Date(now.getTime() - offset);
    document.getElementById('end_time_input').value = localDate.toISOString().slice(0, 16);
    updateDuration();

    // Clear any accidentally applied loading state
    const element = event && event.target;
    if (element && element.classList.contains('loading')) {
        element.classList.remove('loading');
    }
}
@endif

// Utility functions
function setPostictalToNow() {
    const now = new Date();
    const offset = now.getTimezoneOffset() * 60000;
    const localDate = new Date(now.getTime() - offset);
    document.getElementById('postictal_state_end').value = localDate.toISOString().slice(0, 16);

    // Clear any accidentally applied loading state
    const button = event.target;
    if (button && button.classList.contains('loading')) {
        button.classList.remove('loading');
    }
}

function openNotesModal() {
    const modal = document.getElementById('notes_modal');
    const notesTextarea = document.getElementById('notes');
    const fullscreenTextarea = document.getElementById('fullscreen_notes');

    fullscreenTextarea.value = notesTextarea.value;
    modal.showModal();
    fullscreenTextarea.focus();
}

function closeNotesModal() {
    document.getElementById('notes_modal').close();
}

function saveNotes() {
    const notesTextarea = document.getElementById('notes');
    const fullscreenTextarea = document.getElementById('fullscreen_notes');

    notesTextarea.value = fullscreenTextarea.value;
    closeNotesModal();
}

function cancelForm() {
    if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
        @if($cancelUrl)
            window.location.href = '{{ $cancelUrl }}';
        @else
            history.back();
        @endif
    }
}

// Initialize severity display on page load
document.addEventListener('DOMContentLoaded', function() {
    updateSeverityDisplay({{ $defaultSeverity }});

    // Update user selection if field exists
    if (document.getElementById('user_select')) {
        document.getElementById('user_select').addEventListener('change', function() {
            document.getElementById('form_user_id').value = this.value;
        });
    }
});
</script>
