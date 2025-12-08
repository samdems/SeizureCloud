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
    <x-form-field
        name="seizure_type"
        label="Type of Seizure"
        type="select"
        :value="old('seizure_type', $seizure?->seizure_type)"
        placeholder="Select seizure type"
        :options="[
            'focal_aware' => 'Focal Aware (Simple Partial)',
            'focal_impaired' => 'Focal Impaired Awareness (Complex Partial)',
            'focal_motor' => 'Focal Motor',
            'focal_non_motor' => 'Focal Non-Motor',
            'generalized_tonic_clonic' => 'Generalized Tonic-Clonic',
            'absence' => 'Absence',
            'myoclonic' => 'Myoclonic',
            'atonic' => 'Atonic (Drop Attack)',
            'tonic' => 'Tonic',
            'clonic' => 'Clonic',
            'unknown' => 'Unknown/Uncertain'
        ]"
        optional
    />

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
            <x-form-field
                name="video_notes"
                label="Video Notes"
                type="textarea"
                :value="old('video_notes', $seizure?->video_notes)"
                placeholder="Notes about video evidence (location, who recorded, etc.)"
                rows="2"
                optional
            />
        </div>
        @error('has_video_evidence')
            <label class="label">
                <span class="label-text-alt text-error">{{ $message }}</span>
            </label>
        @enderror
    </div>

    <!-- Possible Triggers -->
    <x-checkbox-group
        name="triggers"
        label="Possible Triggers"
        help-text="Select all that may have contributed"
        :values="old('triggers', $seizure?->triggers ?? [])"
        :options="[
            'stress' => 'Stress',
            'lack_of_sleep' => 'Lack of sleep',
            'missed_medication' => 'Missed medication',
            'illness' => 'Illness/fever',
            'alcohol' => 'Alcohol',
            'flashing_lights' => 'Flashing lights',
            'hormonal' => 'Hormonal changes',
            'dehydration' => 'Dehydration',
            'low_blood_sugar' => 'Low blood sugar'
        ]"
        columns="md:grid-cols-2 lg:grid-cols-3"
        optional
    />
    <x-form-field
        name="other_triggers"
        label="Other Triggers"
        type="textarea"
        :value="old('other_triggers', $seizure?->other_triggers)"
        placeholder="Other triggers not listed above"
        rows="2"
        wrapper-class="mt-3"
        optional
    />

    <!-- Pre-ictal Symptoms -->
    <x-checkbox-group
        name="pre_ictal_symptoms"
        label="Pre-ictal Symptoms"
        help-text="What did you notice before the seizure?"
        :values="old('pre_ictal_symptoms', $seizure?->pre_ictal_symptoms ?? [])"
        :options="[
            'aura' => 'Aura/warning feeling',
            'mood_change' => 'Mood changes',
            'headache' => 'Headache',
            'confusion' => 'Confusion',
            'unusual_sensations' => 'Unusual sensations',
            'none_noticed' => 'No warning signs'
        ]"
        columns="md:grid-cols-2"
        optional
    />

    <x-form-field
        name="pre_ictal_notes"
        label="Pre-ictal Notes"
        type="textarea"
        :value="old('pre_ictal_notes', $seizure?->pre_ictal_notes)"
        placeholder="Describe any pre-ictal symptoms in detail"
        rows="2"
        wrapper-class="mt-3"
        optional
    />

    <!-- Post-ictal Recovery -->
    <div class="space-y-4">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-base-content">Post-ictal Recovery</h3>
            <p class="text-sm text-gray-500 mt-1">How did you feel after the seizure?</p>
        </div>

        <x-form-field
            name="recovery_time"
            label="Recovery Time"
            type="select"
            :value="old('recovery_time', $seizure?->recovery_time)"
            placeholder="Select recovery time"
            :options="[
                'immediate' => 'Immediate (< 5 minutes)',
                'short' => 'Short (5-30 minutes)',
                'moderate' => 'Moderate (30min - 2 hours)',
                'long' => 'Long (2-6 hours)',
                'very_long' => 'Very long (6+ hours)'
            ]"
            optional
        />

        <div class="space-y-2">
            <label class="block text-sm font-semibold text-base-content mb-2">
                Post-ictal Symptoms
                <span class="text-sm text-gray-500 ml-1">(Optional)</span>
                <span class="block text-xs text-gray-500 mt-1 font-normal">Select any symptoms you experienced after the seizure</span>
            </label>

            <div class="space-y-2">
                <x-form-field
                    name="slept_after"
                    label="Slept after seizure"
                    type="checkbox"
                    :value="old('slept_after', $seizure?->slept_after)"
                    class="checkbox-primary checkbox-sm"
                    wrapper-class="mb-2"
                />

                <x-form-field
                    name="post_ictal_confusion"
                    label="Post-ictal confusion"
                    type="checkbox"
                    :value="old('post_ictal_confusion', $seizure?->post_ictal_confusion)"
                    class="checkbox-primary checkbox-sm"
                    wrapper-class="mb-2"
                />

                <x-form-field
                    name="post_ictal_headache"
                    label="Post-ictal headache"
                    type="checkbox"
                    :value="old('post_ictal_headache', $seizure?->post_ictal_headache)"
                    class="checkbox-primary checkbox-sm"
                    wrapper-class="mb-2"
                />
            </div>
        </div>

        <x-form-field
            name="recovery_notes"
            label="Recovery Notes"
            type="textarea"
            :value="old('recovery_notes', $seizure?->recovery_notes)"
            placeholder="Describe your recovery experience"
            rows="3"
            optional
        />
    </div>

    <!-- Medical Information -->
    <div class="divider">Medical Information</div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Emergency Response -->
        <div class="space-y-4">
            <x-form-field
                name="ambulance_called"
                label="Ambulance Called"
                type="checkbox"
                :value="old('ambulance_called', $seizure?->ambulance_called)"
                class="checkbox-primary"
                optional
            />

            <x-form-field
                name="nhs_contact_type"
                label="NHS Contact"
                type="select"
                :value="old('nhs_contact_type', $seizure?->nhs_contact_type)"
                placeholder="No contact"
                :options="[
                    'gp' => 'GP',
                    'hospital' => 'Hospital',
                    '111' => 'NHS 111',
                    '999' => 'Emergency (999)'
                ]"
                optional
            />
        </div>

        <!-- Period & Hormones -->
        <div class="space-y-4">
            <x-form-field
                name="on_period"
                label="On Period"
                type="checkbox"
                :value="old('on_period', $seizure?->on_period)"
                class="checkbox-primary"
                optional
            />

            <x-form-field
                name="days_since_period"
                label="Days Since Last Period Started"
                type="number"
                :value="old('days_since_period', $seizure?->days_since_period)"
                placeholder="Optional"
                min="0"
                max="100"
                optional
            />
        </div>
    </div>

    <!-- Medication Adherence -->
    <x-form-field
        name="medication_adherence"
        label="How well did you take your epilepsy medicines recently?"
        type="select"
        :value="old('medication_adherence', $seizure?->medication_adherence)"
        placeholder="Select adherence level"
        :options="[
            'excellent' => 'Excellent (100% of doses)',
            'good' => 'Good (75-99% of doses)',
            'fair' => 'Fair (50-74% of doses)',
            'poor' => 'Poor (< 50% of doses)'
        ]"
        onchange="toggleMedicationDetails()"
        optional
    />

    <div id="medication_details" class="space-y-2" style="display: none;">
        <div class="form-control mt-3">
            <label class="block text-sm font-semibold text-base-content mb-2">
                Medication Issues
                <span class="text-sm text-gray-500 ml-1">(Optional)</span>
                <span class="block text-xs text-gray-500 mt-1 font-normal">Select any that apply</span>
            </label>
            <div class="grid grid-cols-1 gap-2">
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
                <label class="cursor-pointer label justify-start gap-2">
                    <input type="checkbox" name="forgot_doses" value="1"
                           {{ old('forgot_doses', $seizure?->forgot_doses) ? 'checked' : '' }}
                           class="checkbox checkbox-primary checkbox-sm">
                    <span class="label-text text-sm">Forgot to take doses</span>
                </label>
                <label class="cursor-pointer label justify-start gap-2">
                    <input type="checkbox" name="ran_out_of_medication" value="1"
                           {{ old('ran_out_of_medication', $seizure?->ran_out_of_medication) ? 'checked' : '' }}
                           class="checkbox checkbox-primary checkbox-sm">
                    <span class="label-text text-sm">Ran out of medication</span>
                </label>
                <label class="cursor-pointer label justify-start gap-2">
                    <input type="checkbox" name="intentionally_skipped" value="1"
                           {{ old('intentionally_skipped', $seizure?->intentionally_skipped) ? 'checked' : '' }}
                           class="checkbox checkbox-primary checkbox-sm">
                    <span class="label-text text-sm">Intentionally skipped doses</span>
                </label>
            </div>
        </div>
    </div>

    <x-form-field
        name="medication_notes"
        label="Medication Notes"
        type="textarea"
        :value="old('medication_notes', $seizure?->medication_notes)"
        placeholder="Notes about medications, changes, or side effects"
        rows="2"
        wrapper-class="mt-3"
        optional
    />

    @error('medication_adherence')
        <div class="label">
            <span class="label-text-alt text-error">{{ $message }}</span>
        </div>
    @enderror

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
        <x-form-field
            name="wellbeing_notes"
            label="Wellbeing Notes"
            type="textarea"
            :value="old('wellbeing_notes', $seizure?->wellbeing_notes)"
            placeholder="Any other observations about your health, mood, or wellbeing"
            rows="2"
            wrapper-class="mt-3"
            optional
        />
        @error('wellbeing_rating')
            <div class="label">
                <span class="label-text-alt text-error">{{ $message }}</span>
            </div>
        @enderror
        @error('sleep_quality')
            <div class="label">
                <span class="label-text-alt text-error">{{ $message }}</span>
            </div>
        @enderror

    </div>

    <!-- Postictal State End -->
    <div class="form-control">
        <label class="block text-sm font-semibold text-base-content mb-2">
            Postictal state ended at
            <button type="button" onclick="setPostictalToNow(event)" class="btn btn-xs btn-primary ml-2" data-no-loading>
                Set to now
            </button>
        </label>
        <input type="datetime-local" name="postictal_state_end" id="postictal_state_end"
               value="{{ old('postictal_state_end', $seizure?->postictal_state_end?->format('Y-m-d\TH:i')) }}"
               class="input input-bordered">
        @error('postictal_state_end')
            <div class="label">
                <span class="label-text-alt text-error">{{ $message }}</span>
            </div>
        @enderror
    </div>

    <!-- Notes -->
    <div class="form-control">
        <label class="block text-sm font-semibold text-base-content mb-2">
            Notes
            <span class="text-sm text-gray-500 ml-1">(Optional)</span>
            <button type="button" onclick="openNotesModal()" class="btn btn-xs btn-outline ml-2">
                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                </svg>
                Expand
            </button>
        </label>
        <textarea name="notes" id="notes" rows="4" class="textarea textarea-bordered"
                  placeholder="Optional notes about the seizure...">{{ old('notes', $seizure?->notes) }}</textarea>
        @error('notes')
            <div class="label">
                <span class="label-text-alt text-error">{{ $message }}</span>
            </div>
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

function toggleMedicationDetails() {
    const select = document.querySelector('select[name="medication_adherence"]');
    const details = document.getElementById('medication_details');

    if (!select || !details) return;

    const value = select.value;

    // Check if any medication issue checkboxes are already checked
    const hasExistingIssues = details.querySelectorAll('input[type="checkbox"]:checked').length > 0;

    // Show details for fair, poor, or good adherence, or if there are existing issues
    if (value === 'fair' || value === 'poor' || value === 'good' || hasExistingIssues) {
        details.style.display = 'block';
    } else if (value === 'excellent') {
        details.style.display = 'none';
        // Only clear checkboxes when selecting excellent adherence
        const checkboxes = details.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(cb => cb.checked = false);
    } else {
        // For empty selection, hide but don't clear existing values
        details.style.display = 'none';
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

    // Initialize medication details visibility
    toggleMedicationDetails();
});
</script>
