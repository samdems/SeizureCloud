<x-layouts.app :title="__('Seizure Record Details')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 max-w-4xl mx-auto">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Seizure Record Details</h1>
            <div class="flex gap-2">
                <x-kebab-menu
                    :items="[
                        [
                            'label' => 'Export PDF',
                            'href' => route('seizures.export.single-pdf', $seizure),
                            'icon' => 'heroicon-o-document-arrow-down',
                        ],
                        [
                            'label' => 'Edit',
                            'href' => route('seizures.edit', $seizure),
                            'icon' => 'heroicon-o-pencil',
                            'wire:navigate' => true,
                        ],

                        [
                            'label' => 'Back to List',
                            'href' => route('seizures.index'),
                            'icon' => 'heroicon-o-arrow-left',
                            'wire:navigate' => true,
                        ],
                    ]"
                />
            </div>
        </div>

        @include('seizures.partials.emergency-status', ['emergencyStatus' => $emergencyStatus])

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-base-content/60 mb-1">Start Time</h3>
                        <p class="text-lg">{{ $seizure->start_time->format('M d, Y H:i') }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-base-content/60 mb-1">End Time</h3>
                        <p class="text-lg">{{ $seizure->end_time ? $seizure->end_time->format('M d, Y H:i') : 'Not recorded' }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-base-content/60 mb-1">Duration</h3>
                        <p class="text-lg flex items-center gap-2">
                            @if($seizure->calculated_duration)
                                {{ $seizure->calculated_duration }} minutes
                                @if($emergencyStatus['status_epilepticus'])
                                    <span class="badge badge-error badge-sm">Possible Status Epilepticus</span>
                                @endif
                            @else
                                Not recorded
                            @endif
                        </p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-base-content/60 mb-1">Severity</h3>
                        <div class="flex gap-1 items-center">
                            @for($i = 1; $i <= 10; $i++)
                            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="{{ $i <= $seizure->severity ? ($seizure->severity <= 3 ? '#10b981' : ($seizure->severity <= 6 ? '#f59e0b' : ($seizure->severity <= 8 ? '#f97316' : '#ef4444'))) : 'none' }}"
                                 stroke="{{ $i <= $seizure->severity ? ($seizure->severity <= 3 ? '#10b981' : ($seizure->severity <= 6 ? '#f59e0b' : ($seizure->severity <= 8 ? '#f97316' : '#ef4444'))) : 'currentColor' }}"
                                 xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10" stroke-width="2" fill="{{ $i <= $seizure->severity ? ($seizure->severity <= 3 ? '#10b981' : ($seizure->severity <= 6 ? '#f59e0b' : ($seizure->severity <= 8 ? '#f97316' : '#ef4444'))) : 'none' }}"/>
                            </svg>
                            @endfor
                            <span class="ml-2 font-semibold">{{ $seizure->severity }}/10</span>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-base-content/60 mb-1">Postictal State End</h3>
                        <p class="text-lg">{{ $seizure->postictal_state_end ? $seizure->postictal_state_end->format('M d, Y H:i') : 'Not recorded' }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-base-content/60 mb-1">Seizure Type</h3>
                        <p class="text-lg">{{ $seizure->seizure_type ?  Str::headline($seizure->seizure_type) : 'Not recorded' }}</p>
                    </div>
                </div>

                <div class="divider"></div>

                <h3 class="text-lg font-semibold mb-4">Triggers</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if ($seizure->triggers)
                    @foreach($seizure->triggers as $trigger)
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">-</span>
                            <span>{{$trigger}}</span>
                        </div>
                    @endforeach
                @else
                <div class="flex items-center gap-2">
                    <div class="text-base-content/60">No triggers recorded.</div>
                </div>
                @endif
                <div class="alert">
                    <p class="whitespace-pre-wrap">{{ $seizure->other_triggers }}</p>
                </div>

                </div>

                <h3 class="text-lg font-semibold mb-4">Pre-Ictal Symptoms</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if(!empty($seizure->pre_ictal_symptoms) && is_array($seizure->pre_ictal_symptoms))
                        @foreach($seizure->pre_ictal_symptoms as $symptom)
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">-</span>
                                <span>{{$symptom}}</span>
                            </div>
                        @endforeach
                    @else
                        <div class="text-base-content/60">No pre-ictal symptoms recorded.</div>
                    @endif
                </div>

                @if($seizure->pre_ictal_notes)
                    <div class="mt-4">
                        <h4 class="font-semibold mb-2">Pre-ictal Notes</h4>
                        <div class="alert">
                            <p class="whitespace-pre-wrap">{{ $seizure->pre_ictal_notes }}</p>
                        </div>
                    </div>
                @endif

                <div class="divider"></div>

                <h3 class="text-lg font-semibold mb-4">Post-ictal Recovery</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-base-content/60 mb-1">Recovery Time</h4>
                        <p class="text-lg">{{ $seizure->recovery_time ? Str::headline($seizure->recovery_time) : 'Not recorded' }}</p>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium text-base-content/60 mb-1">Post-ictal Symptoms</h4>
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="text-lg">{{ $seizure->post_ictal_confusion ? '✓' : '✗' }}</span>
                                <span class="{{ $seizure->post_ictal_confusion ? 'text-success' : 'text-base-content/40' }}">Post-ictal confusion</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-lg">{{ $seizure->post_ictal_headache ? '✓' : '✗' }}</span>
                                <span class="{{ $seizure->post_ictal_headache ? 'text-success' : 'text-base-content/40' }}">Post-ictal headache</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($seizure->recovery_notes)
                    <div class="mt-4">
                        <h4 class="font-semibold mb-2">Recovery Notes</h4>
                        <div class="alert">
                            <p class="whitespace-pre-wrap">{{ $seizure->recovery_notes }}</p>
                        </div>
                    </div>
                @endif

                <div class="divider"></div>

                <h3 class="text-lg font-semibold mb-4">Additional Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">{{ $seizure->on_period ? '✓' : '✗' }}</span>
                        <span class="{{ $seizure->on_period ? 'text-success' : 'text-base-content/40' }}">
                            On Period
                            @if($seizure->days_since_period)
                                 ({{$seizure->days_since_period}} days since last period)
                            @endif
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-2xl">{{ $seizure->ambulance_called ? '✓' : '✗' }}</span>
                        <span class="{{ $seizure->ambulance_called ? 'text-success' : 'text-base-content/40' }}">Ambulance Called</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-2xl">{{ $seizure->slept_after ? '✓' : '✗' }}</span>
                        <span class="{{ $seizure->slept_after ? 'text-success' : 'text-base-content/40' }}">Slept After</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-2xl">{{ $seizure->nhs_contact_type ? '✓' : '✗' }}</span>
                        <span class="{{ $seizure->nhs_contact_type ? 'text-success' : 'text-base-content/40' }}">
                            NHS Contacted
                            @if($seizure->nhs_contact_type)
                                ({{ $seizure->nhs_contact_type }})
                            @endif
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-2xl">{{ $seizure->has_video_evidence ? '✓' : '✗' }}</span>
                        <span class="{{ $seizure->has_video_evidence ? 'text-success' : 'text-base-content/40' }}">video evidence</span>
                    </div>
                </div>
                @if($seizure->has_video_evidence || $seizure->video_file_path)
                    <div class="divider"></div>

                    <h3 class="text-lg font-semibold mb-4">Video Evidence</h3>

                    @if($seizure->video_file_path)
                        <div class="card bg-base-200 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="font-semibold text-success">Video Available</span>
                                    </div>
                                    <div class="flex gap-2">
                                        @if($seizure->hasValidVideo())
                                            <a href="{{ $seizure->getVideoPublicUrl() }}"
                                               target="_blank"
                                               class="btn btn-sm btn-primary">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h1m4 0h1m-3-8a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                </svg>
                                                View Video
                                            </a>
                                            <a href="{{ $seizure->getVideoPublicUrl() }}?download=1"
                                               class="btn btn-sm btn-outline">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-4-4m4 4l4-4m-6 4h8"></path>
                                                </svg>
                                                Download
                                            </a>
                                        @endif
                                        <form method="POST" action="{{ route('seizures.video.delete', $seizure) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-error"
                                                    onclick="return confirm('Are you sure you want to delete this video?')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="text-sm space-y-1">
                                    <div class="flex justify-between">
                                        <span class="text-base-content/70">File Size:</span>
                                        <span>{{ app('App\Services\VideoUploadService')->getVideoSize($seizure) ?? 'Unknown' }} MB</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-base-content/70">Direct Link:</span>
                                        <button onclick="copyToClipboard('{{ $seizure->getVideoPublicUrl() }}')"
                                                class="btn btn-xs btn-outline">
                                            Copy Link
                                        </button>
                                    </div>
                                </div>

                                <div class="mt-3 flex gap-2">
                                    <form method="POST" action="{{ route('seizures.video.regenerate-token', $seizure) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-outline">
                                            Regenerate Access Link
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Video Upload Form -->
                        <form method="POST" action="{{ route('seizures.video.upload', $seizure) }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Upload Video File</span>
                                    <span class="label-text-alt">Max {{ App\Services\VideoUploadService::getMaxFileSizeMB() }}MB</span>
                                </label>
                                <input type="file"
                                       name="video"
                                       class="file-input file-input-bordered"
                                       accept="video/mp4,video/quicktime,video/x-msvideo,video/x-matroska,video/webm">
                                <div class="label">
                                    <span class="label-text-alt text-base-content/60">
                                        Supported formats: {{ implode(', ', App\Services\VideoUploadService::getAllowedExtensions()) }}
                                    </span>
                                </div>
                                @error('video')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                Upload Video
                            </button>
                        </form>
                    @endif

                    @if($seizure->video_notes)
                        <h4 class="text-lg font-semibold mb-2 mt-4">Video Notes</h4>
                        <div class="alert">
                            <p class="whitespace-pre-wrap">{{ $seizure->video_notes }}</p>
                        </div>
                    @endif
                @endif

                @if($seizure->notes)
                    <div class="divider"></div>

                    <h3 class="text-lg font-semibold mb-4">Notes</h3>
                    <div class="alert">
                        <p class="whitespace-pre-wrap">{{ $seizure->notes }}</p>
                    </div>
                @endif

                <div class="divider"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Wellbeing Rating</h3>
                        @if($seizure->wellbeing_rating)
                            <p class="text-lg">{{ Str::headline($seizure->wellbeing_rating) }}</p>
                        @else
                            <p class="text-lg text-base-content/60">No wellbeing rating recorded.</p>
                        @endif
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4">Sleep Quality</h3>
                        @if($seizure->sleep_quality)
                            <p class="text-lg">{{ Str::headline($seizure->sleep_quality) }}</p>
                        @else
                            <p class="text-lg text-base-content/60">No sleep quality recorded.</p>
                        @endif
                    </div>
                </div>

                @if($seizure->wellbeing_notes)
                    <h3 class="text-lg font-semibold mb-4">Wellbeing Notes</h3>
                    <div class="alert">
                        <p class="whitespace-pre-wrap">{{ $seizure->wellbeing_notes }}</p>
                    </div>
                @endif

                <div class="divider"></div>

                <h3 class="text-lg font-semibold mb-4">Medication Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h4 class="text-sm font-medium text-base-content/60 mb-1">Medication Adherence</h4>
                        <p class="text-lg">{{ $seizure->medication_adherence ? Str::headline($seizure->medication_adherence) : 'Not recorded' }}</p>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium text-base-content/60 mb-1">Recent Changes & Side Effects</h4>
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="text-lg">{{ $seizure->recent_medication_change ? '✓' : '✗' }}</span>
                                <span class="{{ $seizure->recent_medication_change ? 'text-warning' : 'text-base-content/40' }}">Recent medication change</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-lg">{{ $seizure->experiencing_side_effects ? '✓' : '✗' }}</span>
                                <span class="{{ $seizure->experiencing_side_effects ? 'text-warning' : 'text-base-content/40' }}">Experiencing side effects</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($seizure->medication_notes)
                    <div class="mb-6">
                        <h4 class="font-semibold mb-2">Medication Notes</h4>
                        <div class="alert">
                            <p class="whitespace-pre-wrap">{{ $seizure->medication_notes }}</p>
                        </div>
                    </div>
                @endif

                <h4 class="text-lg font-semibold mb-4">Detailed Medication Adherence</h4>
                @if($medications->isEmpty())
                    <p class="text-base-content/60">No active medications at the time of this seizure.</p>
                @else
                    <div class="space-y-6">
                        @foreach($medications as $medication)
                            @php
                                $adherence = $medication->adherence;
                                $wasNeeded = $adherence['was_needed'];
                                $allTaken = $adherence['all_taken'];
                            @endphp

                            <div class="card bg-base-100 border {{ !$wasNeeded ? 'opacity-60' : '' }}">
                                <div class="card-header bg-base-200 px-6 py-4 border-b">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="font-semibold text-lg {{ !$wasNeeded ? 'text-base-content/50' : '' }}">
                                                {{ $medication->name }}
                                            </h4>
                                            <p class="text-sm text-base-content/60">
                                                {{ $medication->dosage }} {{ $medication->unit }}
                                            </p>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            @if(!$wasNeeded)
                                                <span class="badge badge-ghost badge-lg">Not Scheduled</span>
                                            @elseif($allTaken)
                                                <span class="badge badge-success badge-lg gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    All Taken
                                                </span>
                                            @else
                                                <span class="badge badge-error badge-lg gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    Missed Doses
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body p-0">
                                    @if($wasNeeded && count($adherence['scheduled_doses']) > 0)
                                        <div class="overflow-x-auto">
                                            <table class="table table-zebra w-full">
                                                <thead>
                                                    <tr>
                                                        <th class="w-16">Status</th>
                                                        <th class="w-32">Scheduled Time</th>
                                                        <th class="w-32">Actual Time</th>
                                                        <th>Notes</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($adherence['scheduled_doses'] as $dose)
                                                        @php
                                                            $wasTaken = $dose['log'] && $dose['log']->taken_at;
                                                            $wasLate = false;
                                                            if($wasTaken) {
                                                                $scheduledTime = $dose['schedule']->scheduled_time;
                                                                $actualTime = $dose['log']->taken_at;
                                                                $diffMinutes = $scheduledTime->diffInMinutes($actualTime, false);
                                                                $wasLate = $diffMinutes > 30;
                                                            }
                                                        @endphp
                                                        <tr class="{{ $wasTaken ? '' : 'bg-error/5' }}">
                                                            <td>
                                                                @if($wasTaken)
                                                                    <div class="flex items-center gap-2">
                                                                        <svg class="w-5 h-5 {{ $wasLate ? 'text-warning' : 'text-success' }}" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                                        </svg>
                                                                        @if($wasLate)
                                                                            <span class="text-warning font-medium">Taken Late</span>
                                                                        @else
                                                                            <span class="text-success font-medium">Taken</span>
                                                                        @endif
                                                                    </div>
                                                                @else
                                                                    <div class="flex items-center gap-2">
                                                                        <svg class="w-5 h-5 text-error" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                                        </svg>
                                                                        <span class="text-error font-medium">Missed</span>
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td class="font-medium">
                                                                {{ $dose['schedule']->scheduled_time->format('g:i A') }}
                                                            </td>
                                                            <td>
                                                                @if($wasTaken)
                                                                    <span class="{{ $wasLate ? 'text-warning' : 'text-success' }}">
                                                                        {{ $dose['log']->taken_at->format('g:i A') }}
                                                                    </span>
                                                                    @if(abs($diffMinutes) > 30)
                                                                        <div class="text-xs text-warning mt-1">
                                                                            @if($diffMinutes > 0)
                                                                                {{ $diffMinutes }}min late
                                                                            @else
                                                                                {{ abs($diffMinutes) }}min early
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                @else
                                                                    <span class="text-error">-</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($dose['log'] && $dose['log']->notes)
                                                                    <span class="text-sm">{{ $dose['log']->notes }}</span>
                                                                @elseif(!$dose['taken'] && $dose['log'] && $dose['log']->skip_reason)
                                                                    <span class="text-sm text-error">{{ $dose['log']->skip_reason }}</span>
                                                                @else
                                                                    <span class="text-base-content/40">-</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @elseif(!$wasNeeded)
                                        <div class="p-6 text-center text-base-content/60">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mx-auto mb-2 opacity-50">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p>No doses were scheduled before the seizure occurred</p>
                                            <p class="text-sm mt-1">Seizure happened at {{ $seizure->start_time->format('g:i A') }}</p>
                                        </div>
                                    @else
                                        <div class="p-6 text-center text-base-content/60">
                                            <p>Medication was needed but no dose information available</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif



                <div class="divider"></div>



                <h3 class="text-lg font-semibold mb-4">Vitals on Day of Seizure</h3>
                @if($vitals->isEmpty())
                    <div class="alert alert-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                        <div>
                            <h4 class="font-semibold">No vitals recorded</h4>
                            <p class="text-sm">No vital signs were recorded on {{ $seizure->start_time->format('M d, Y') }}</p>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($vitals as $type => $typeVitals)
                            <div class="card bg-base-200">
                                <div class="card-body p-4">
                                    <h4 class="font-semibold text-lg mb-2 flex items-center gap-2">
                                        @if($type === 'Heart Rate')
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-red-500">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                            </svg>
                                        @elseif(str_contains($type, 'Blood Pressure'))
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-blue-500">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423L16.5 15.75l.394 1.183a2.25 2.25 0 001.423 1.423L19.5 18.75l-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                                            </svg>
                                        @elseif($type === 'Weight')
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-green-500">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.254 48.254 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52l2.62 10.726c.122.499-.106 1.028-.589 1.36a5.74 5.74 0 01-1.528.649m-8.063 0a5.74 5.74 0 01-1.528-.649c-.483-.332-.711-.861-.589-1.36L8.25 5.47m9.5 0L16.5 8.5m.75-3.03L15.75 8.5" />
                                            </svg>
                                        @elseif($type === 'Body Temperature')
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-orange-500">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 10.5h.375c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125H21M4.5 10.5H4.125C3.504 10.5 3 11.004 3 11.625v2.25c0 .621.504 1.125 1.125 1.125H4.5m6.75-9.75h.375c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-.375a1.125 1.125 0 01-1.125-1.125V5.625c0-.621.504-1.125 1.125-1.125z" />
                                            </svg>
                                        @elseif($type === 'Blood Oxygen Level')
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-cyan-500">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 004.5 4.5H18a3.75 3.75 0 001.332-7.257 3 3 0 00-3.758-3.848 5.25 5.25 0 00-10.233 2.33A4.502 4.502 0 002.25 15z" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-purple-500">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                            </svg>
                                        @endif
                                        {{ $type }}
                                    </h4>

                                    <div class="space-y-2">
                                        @foreach($typeVitals as $vital)
                                            <div class="flex justify-between items-center p-2 bg-base-100 rounded">
                                                <div>
                                                    <span class="font-medium text-lg">
                                                        {{ number_format($vital->value, 1) }}
                                                        @if($type === 'Heart Rate')
                                                            bpm
                                                        @elseif(str_contains($type, 'Blood Pressure'))
                                                            mmHg
                                                        @elseif($type === 'Weight')
                                                            kg
                                                        @elseif($type === 'Body Temperature')
                                                            °C
                                                        @elseif($type === 'Blood Oxygen Level')
                                                            %
                                                        @elseif($type === 'Respiratory Rate')
                                                            /min
                                                        @elseif($type === 'Blood Sugar')
                                                            mg/dL
                                                        @endif
                                                    </span>
                                                    <div class="text-xs text-base-content/60">
                                                        {{ $vital->recorded_at->format('H:i') }}
                                                        @if($vital->recorded_at <= $seizure->start_time)
                                                            <span class="badge badge-xs badge-info">Before seizure</span>
                                                        @else
                                                            <span class="badge badge-xs badge-warning">After seizure</span>
                                                        @endif
                                                    </div>
                                                    @if($vital->notes)
                                                        <div class="text-xs text-base-content/50 italic mt-1">
                                                            {{ Str::limit($vital->notes, 30) }}
                                                        </div>
                                                    @endif
                                                </div>

                                                @php
                                                    $isAbnormal = false;
                                                    $abnormalClass = '';

                                                    // Define normal ranges and check for abnormal values
                                                    switch($type) {
                                                        case 'Heart Rate':
                                                            $isAbnormal = $vital->value < 60 || $vital->value > 100;
                                                            $abnormalClass = $vital->value < 60 ? 'text-blue-500' : ($vital->value > 100 ? 'text-red-500' : '');
                                                            break;
                                                        case 'Blood Pressure Systolic':
                                                            $isAbnormal = $vital->value < 90 || $vital->value > 140;
                                                            $abnormalClass = $vital->value < 90 ? 'text-blue-500' : ($vital->value > 140 ? 'text-red-500' : '');
                                                            break;
                                                        case 'Blood Pressure Diastolic':
                                                            $isAbnormal = $vital->value < 60 || $vital->value > 90;
                                                            $abnormalClass = $vital->value < 60 ? 'text-blue-500' : ($vital->value > 90 ? 'text-red-500' : '');
                                                            break;
                                                        case 'Body Temperature':
                                                            $isAbnormal = $vital->value < 36 || $vital->value > 37.5;
                                                            $abnormalClass = $vital->value < 36 ? 'text-blue-500' : ($vital->value > 37.5 ? 'text-red-500' : '');
                                                            break;
                                                        case 'Blood Oxygen Level':
                                                            $isAbnormal = $vital->value < 95;
                                                            $abnormalClass = $vital->value < 95 ? 'text-red-500' : '';
                                                            break;
                                                        case 'Blood Sugar':
                                                            $isAbnormal = $vital->value < 70 || $vital->value > 140;
                                                            $abnormalClass = $vital->value < 70 ? 'text-blue-500' : ($vital->value > 140 ? 'text-red-500' : '');
                                                            break;
                                                    }
                                                @endphp

                                                @if($isAbnormal)
                                                    <div class="flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 {{ $abnormalClass }}">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @php
                        $totalVitals = $vitals->flatten()->count();
                        $beforeSeizure = $vitals->flatten()->filter(fn($v) => $v->recorded_at <= $seizure->start_time)->count();
                        $afterSeizure = $totalVitals - $beforeSeizure;
                    @endphp

                    <div class="mt-4 p-4 bg-base-200 rounded-lg">
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex gap-4">
                                <span><strong>Total recordings:</strong> {{ $totalVitals }}</span>
                                <span><strong>Before seizure:</strong> {{ $beforeSeizure }}</span>
                                <span><strong>After seizure:</strong> {{ $afterSeizure }}</span>
                            </div>
                            <div class="text-xs text-base-content/60">
                                Seizure occurred at {{ $seizure->start_time->format('H:i') }}
                            </div>
                        </div>
                    </div>
                @endif

                <div class="divider"></div>

                <div class="text-sm text-base-content/60">
                    <p>Created: {{ $seizure->created_at->format('M d, Y H:i') }}</p>
                    @if($seizure->updated_at != $seizure->created_at)
                        <p>Last Updated: {{ $seizure->updated_at->format('M d, Y H:i') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex justify-between">
            <form action="{{ route('seizures.destroy', $seizure) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-error">
                    Delete Record
                </button>
            </form>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show success message
                const toast = document.createElement('div');
                toast.className = 'toast toast-top toast-end';
                toast.innerHTML = `
                    <div class="alert alert-success">
                        <span>Video link copied to clipboard!</span>
                    </div>
                `;
                document.body.appendChild(toast);

                // Remove toast after 3 seconds
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 3000);
            }).catch(function(err) {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);

                // Show success message
                const toast = document.createElement('div');
                toast.className = 'toast toast-top toast-end';
                toast.innerHTML = `
                    <div class="alert alert-success">
                        <span>Video link copied to clipboard!</span>
                    </div>
                `;
                document.body.appendChild(toast);

                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 3000);
            });
        }
    </script>
</x-layouts.app>
