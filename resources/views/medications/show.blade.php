<x-layouts.app :title="__('Medication Details')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 max-w-6xl mx-auto">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">{{ $medication->name }}</h1>
            <div class="flex gap-2">
                <x-kebab-menu
                    :items="[
                        [
                            'label' => 'Edit',
                            'href' => route('medications.edit', $medication),
                            'icon' => 'heroicon-o-pencil',
                            'wire:navigate' => true,
                        ],

                        [
                            'label' => 'Back to List',
                            'href' => route('medications.index'),
                            'icon' => 'heroicon-o-arrow-left',
                            'wire:navigate' => true,
                        ],
                    ]"
                />
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Medication Info -->
            <div class="lg:col-span-2 card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">Medication Information</h2>

                    <div class="mb-4">
                        <a href="https://bnf.nice.org.uk/drugs/{{ urlencode(strtolower($medication->name)) }}/"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn btn-sm btn-outline btn-primary">
                            <x-heroicon-o-book-open class="h-4 w-4" />
                            View in BNF
                            <x-heroicon-o-arrow-top-right-on-square class="h-3 w-3" />
                        </a>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-sm font-medium text-base-content/60">Dosage</h3>
                            <p class="text-lg">{{ $medication->dosage }} {{ $medication->unit }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-base-content/60">Status</h3>
                            <p>
                                @if($medication->active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-ghost">Inactive</span>
                                @endif
                                @if($medication->as_needed)
                                    <span class="badge badge-secondary ml-2">PRN (As Needed)</span>
                                @endif
                            </p>
                        </div>

                        @if($medication->description)
                            <div class="col-span-2">
                                <h3 class="text-sm font-medium text-base-content/60">Description</h3>
                                <p>{{ $medication->description }}</p>
                            </div>
                        @endif

                        @if($medication->prescriber)
                            <div>
                                <h3 class="text-sm font-medium text-base-content/60">Prescribed By</h3>
                                <p>{{ $medication->prescriber }}</p>
                            </div>
                        @endif

                        @if($medication->start_date)
                            <div>
                                <h3 class="text-sm font-medium text-base-content/60">Start Date</h3>
                                <p>{{ $medication->start_date->format('M d, Y') }}</p>
                            </div>
                        @endif

                        @if($medication->end_date)
                            <div>
                                <h3 class="text-sm font-medium text-base-content/60">End Date</h3>
                                <p>{{ $medication->end_date->format('M d, Y') }}</p>
                            </div>
                        @endif

                        @if($medication->notes)
                            <div class="col-span-2">
                                <h3 class="text-sm font-medium text-base-content/60">Notes</h3>
                                <div class="alert">{{ $medication->notes }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">Quick Actions</h2>
                    <button class="btn btn-success btn-block" onclick="quickLogModal.showModal()">
                        <x-heroicon-o-check class="h-5 w-5" />
                        Log Taken Now
                    </button>
                    @if(!$medication->as_needed)
                        <button class="btn btn-primary btn-block" onclick="addScheduleModal.showModal()">
                            <x-heroicon-o-plus class="h-5 w-5" />
                            Add Schedule
                        </button>
                    @endif
                    <form action="{{ route('medications.destroy', $medication) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-error btn-block">Delete Medication</button>
                    </form>
                </div>
            </div>
        </div>

        @if(!$medication->as_needed)
            <!-- Schedules -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="flex justify-between items-center">
                        <h2 class="card-title">Schedules</h2>
                        <button class="btn btn-primary btn-sm" onclick="addScheduleModal.showModal()">
                            <x-heroicon-o-plus class="h-4 w-4" />
                            Add Schedule
                        </button>
                    </div>

                    @if($medication->schedules->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Dosage</th>
                                        <th>Frequency</th>
                                        <th>Days</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($medication->schedules as $schedule)
                                        <tr>
                                            <td class="font-semibold">{{ Carbon\Carbon::parse($schedule->scheduled_time)->format('g:i A') }}</td>
                                            <td>
                                                @if($schedule->dosage_multiplier)
                                                    {{ $schedule->dosage_multiplier }} {{ $schedule->unit ?? $medication->unit }}
                                                @else
                                                    <span class="text-base-content/50">Not specified</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge">{{ ucfirst($schedule->frequency) }}</span>
                                            </td>
                                            <td>
                                                @if($schedule->frequency === 'weekly' && $schedule->days_of_week)
                                                    @php
                                                        $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                                                        $scheduledDays = collect($schedule->days_of_week)->map(fn($d) => $days[$d])->join(', ');
                                                    @endphp
                                                    {{ $scheduledDays }}
                                                @else
                                                    Every day
                                                @endif
                                            </td>
                                            <td>
                                                @if($schedule->active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-ghost">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="flex gap-2">
                                                    <button class="btn btn-sm btn-info" onclick="editScheduleModal{{ $schedule->id }}.showModal()">
                                                        <x-heroicon-o-pencil class="h-4 w-4" />
                                                        Edit
                                                    </button>
                                                    <form action="{{ route('medications.schedules.destroy', [$medication, $schedule]) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-error" onclick="return confirm('Delete this schedule?')">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert">
                            <span>No schedules set up yet. Add a schedule to track when to take this medication.</span>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <!-- PRN Information -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">PRN Medication</h2>
                    <div class="alert alert-info">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>
                            This is a PRN (as needed) medication. No regular schedule is required - take only when needed as directed by your healthcare provider.
                        </span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Recent Logs -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">Recent History</h2>

                @if($medication->logs->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-zebra">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Status</th>
                                    <th>Dosage</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($medication->logs as $log)
                                    <tr class="{{ $log->isTakenLate() ? 'bg-error/10' : '' }}">
                                        <td>{{ $log->taken_at->format('M d, Y g:i A') }}</td>
                                        <td>
                                            @if($log->skipped)
                                                <span class="badge badge-error">Skipped</span>
                                                @if($log->skip_reason)
                                                    <span class="text-xs">({{ $log->skip_reason }})</span>
                                                @endif
                                            @else
                                                @if($log->isTakenLate())
                                                    <span class="badge badge-error"><x-heroicon-o-exclamation-triangle class="w-4 h-4 inline mr-1" />Taken Late</span>
                                                @else
                                                    <span class="badge badge-success">Taken</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>{{ $log->dosage_taken ?? '-' }}</td>
                                        <td>{{ $log->notes ?? '-' }}</td>
                                        <td>
                                            <div class="flex gap-2">
                                                <button class="btn btn-sm btn-info" onclick="editLogModal{{ $log->id }}.showModal()">
                                                    <x-heroicon-o-pencil class="h-4 w-4" />
                                                    Edit
                                                </button>
                                                <form action="{{ route('medications.log-destroy', $log) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this history entry?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-error">
                                                        <x-heroicon-o-trash class="h-4 w-4" />
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Edit Log Modal -->
                                    <dialog id="editLogModal{{ $log->id }}" class="modal">
                                        <div class="modal-box">
                                            <h3 class="font-bold text-lg">Edit Medication History</h3>

                                            @if ($errors->any())
                                                <div class="alert alert-error mt-4">
                                                    <div>
                                                        <x-heroicon-o-exclamation-triangle class="h-5 w-5" />
                                                        <div>
                                                            <h3 class="font-bold">There were some errors with your submission</h3>
                                                            <ul class="list-disc list-inside text-sm">
                                                                @foreach ($errors->all() as $error)
                                                                    <li>{{ $error }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <form method="POST" action="{{ route('medications.log-update', $log) }}" class="space-y-4 mt-4">
                                                @csrf
                                                @method('PUT')

                                                @if($log->intended_time)
                                                    <input type="hidden" name="intended_time" value="{{ $log->intended_time->format('Y-m-d\TH:i:s') }}">
                                                @endif

                                                <x-form-field
                                                    name="taken_at"
                                                    label="Date & Time"
                                                    type="datetime-local"
                                                    value="{{ $log->taken_at->format('Y-m-d\TH:i') }}"
                                                    required
                                                />

                                                <div class="form-control">
                                                    <label class="label">
                                                        <span class="label-text">Status</span>
                                                    </label>
                                                    <div class="flex gap-4">
                                                        <label class="label cursor-pointer">
                                                            <span class="label-text mr-2">Taken</span>
                                                            <input type="radio" name="skipped" value="0" class="radio radio-primary" {{ !$log->skipped ? 'checked' : '' }}>
                                                        </label>
                                                        <label class="label cursor-pointer">
                                                            <span class="label-text mr-2">Skipped</span>
                                                            <input type="radio" name="skipped" value="1" class="radio radio-error" {{ $log->skipped ? 'checked' : '' }} onchange="toggleSkipReason{{ $log->id }}()">
                                                        </label>
                                                    </div>
                                                </div>

                                                <div id="skipReasonDiv{{ $log->id }}" class="form-control {{ !$log->skipped ? 'hidden' : '' }}">
                                                    <x-form-field
                                                        name="skip_reason"
                                                        label="Reason for Skipping"
                                                        type="select"
                                                        placeholder="Select reason..."
                                                        value="{{ $log->skip_reason }}"
                                                        :options="[
                                                            'Forgot' => 'Forgot',
                                                            'Side effects' => 'Side effects',
                                                            'Ran out' => 'Ran out',
                                                            'Felt better' => 'Felt better',
                                                            'Other' => 'Other'
                                                        ]"
                                                    />
                                                </div>

                                                <x-form-field
                                                    name="dosage_taken"
                                                    label="Dosage"
                                                    type="text"
                                                    value="{{ $log->dosage_taken }}"
                                                    optional
                                                />

                                                <x-form-field
                                                    name="notes"
                                                    label="Notes"
                                                    type="textarea"
                                                    rows="3"
                                                    value="{{ $log->notes }}"
                                                    optional
                                                />

                                                <div class="modal-action">
                                                    <button type="button" class="btn btn-outline" onclick="editLogModal{{ $log->id }}.close()">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <x-heroicon-o-check class="h-4 w-4" />
                                                        Update History
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                        <form method="dialog" class="modal-backdrop">
                                            <button>close</button>
                                        </form>
                                    </dialog>

                                    <script>
                                        function toggleSkipReason{{ $log->id }}() {
                                            const skippedRadio = document.querySelector('#editLogModal{{ $log->id }} input[name="skipped"][value="1"]:checked');
                                            const skipReasonDiv = document.getElementById('skipReasonDiv{{ $log->id }}');

                                            if (skippedRadio) {
                                                skipReasonDiv.classList.remove('hidden');
                                            } else {
                                                skipReasonDiv.classList.add('hidden');
                                            }
                                        }

                                        // Add event listeners for the radio buttons for this specific modal
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const radioButtons = document.querySelectorAll('#editLogModal{{ $log->id }} input[name="skipped"]');
                                            radioButtons.forEach(function(radio) {
                                                radio.addEventListener('change', function() {
                                                    toggleSkipReason{{ $log->id }}();
                                                });
                                            });

                                            // Add form submit event listener for debugging
                                            const form = document.querySelector('#editLogModal{{ $log->id }} form');
                                            if (form) {
                                                form.addEventListener('submit', function(e) {
                                                    console.log('Form submitting for log {{ $log->id }}');
                                                    console.log('Form action:', this.action);
                                                    console.log('Form method:', this.method);

                                                    // Check if form is valid
                                                    if (!this.checkValidity()) {
                                                        console.log('Form validation failed');
                                                        e.preventDefault();
                                                        this.reportValidity();
                                                        return false;
                                                    }
                                                });
                                            }
                                        });
                                    </script>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert">
                        <span>No logs yet. Start tracking by logging when you take this medication.</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Log Modal -->
    <dialog id="quickLogModal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Log Medication Taken</h3>
            <form method="POST" action="{{ route('medications.log-taken') }}" class="space-y-4 mt-4">
                @csrf
                <input type="hidden" name="medication_id" value="{{ $medication->id }}">
                <input type="hidden" name="taken_at" value="{{ now()->format('Y-m-d\TH:i') }}">
                <input type="hidden" name="dosage_taken" value="{{ $medication->dosage }} {{ $medication->unit }}">

                <div class="alert alert-info">
                    <span>Logging as taken now at {{ now()->format('g:i A') }}</span>
                </div>

                <x-form-field
                    name="notes"
                    label="Notes"
                    type="textarea"
                    rows="2"
                    optional
                />

                <div class="modal-action">
                    <button type="button" class="btn btn-outline" onclick="quickLogModal.close()">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <x-heroicon-o-check class="h-4 w-4" />
                        Log Taken
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    @if(!$medication->as_needed)
        <!-- Add Schedule Modal -->
        <dialog id="addScheduleModal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Add Schedule</h3>
            <form method="POST" action="{{ route('medications.schedules.store', $medication) }}" class="space-y-4 mt-4">
                @csrf

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Time *</span>
                    </label>
                    <div class="flex flex-wrap gap-2 mb-2">
                        <button type="button" class="btn btn-sm btn-outline" onclick="setScheduleTime('08:00')">
                            Morning (8 AM)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline" onclick="setScheduleTime('12:00')">
                            Around Noon
                        </button>
                        <button type="button" class="btn btn-sm btn-outline" onclick="setScheduleTime('18:00')">
                            Evening (6 PM)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline" onclick="setScheduleTime('22:00')">
                            Bedtime (10 PM)
                        </button>
                    </div>
                    <input type="time" name="scheduled_time" id="scheduled_time" class="input input-bordered" required>
                </div>

                <x-form-field
                    name="dosage_multiplier"
                    label="Dosage"
                    type="number"
                    :value="$medication->dosage ?? ''"
                    step="0.01"
                    min="0.01"
                />

                <x-form-field
                    name="unit"
                    label="Unit"
                    type="text"
                    :value="$medication->unit ?? ''"
                    placeholder="e.g., mg, ml, tablets"
                    maxlength="50"
                />

                <x-form-field
                    name="frequency"
                    label="Frequency"
                    type="select"
                    id="frequency"
                    :options="[
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'as_needed' => 'As Needed'
                    ]"
                    onchange="toggleDaysOfWeek()"
                    required
                />

                <div id="daysOfWeekContainer" class="form-control hidden">
                    <label class="label">
                        <span class="label-text">Days of Week</span>
                    </label>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $index => $day)
                            <label class="label cursor-pointer gap-2">
                                <input type="checkbox" name="days_of_week[]" value="{{ $index }}" class="checkbox checkbox-sm">
                                <span class="label-text">{{ $day }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <x-form-field
                    name="active"
                    label="Active"
                    type="checkbox"
                    value="1"
                    class="checkbox checkbox-primary"
                />

                <x-form-field
                    name="notes"
                    label="Notes"
                    type="textarea"
                    rows="2"
                    optional
                />

                <div class="modal-action">
                    <button type="button" class="btn btn-outline" onclick="addScheduleModal.close()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <x-heroicon-o-plus class="h-4 w-4" />
                        Add Schedule
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
        <!-- Edit Schedule Modals -->
        @foreach($medication->schedules as $schedule)
            <dialog id="editScheduleModal{{ $schedule->id }}" class="modal">
                <div class="modal-box">
                    <h3 class="font-bold text-lg">Edit Schedule</h3>
                    <form method="POST" action="{{ route('medications.schedules.update', [$medication, $schedule]) }}" class="space-y-4 mt-4">
                        @csrf
                        @method('PUT')

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Time *</span>
                            </label>
                            <div class="flex flex-wrap gap-2 mb-2">
                                <button type="button" class="btn btn-sm btn-outline" onclick="setEditScheduleTime{{ $schedule->id }}('08:00')">
                                    Morning (8 AM)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline" onclick="setEditScheduleTime{{ $schedule->id }}('12:00')">
                                    Around Noon
                                </button>
                                <button type="button" class="btn btn-sm btn-outline" onclick="setEditScheduleTime{{ $schedule->id }}('18:00')">
                                    Evening (6 PM)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline" onclick="setEditScheduleTime{{ $schedule->id }}('22:00')">
                                    Bedtime (10 PM)
                                </button>
                            </div>
                            <input type="time" name="scheduled_time" id="edit_scheduled_time_{{ $schedule->id }}" class="input input-bordered" value="{{ Carbon\Carbon::parse($schedule->scheduled_time)->format('H:i') }}" required>
                        </div>

                        <x-form-field
                            name="dosage_multiplier"
                            label="Dosage"
                            type="number"
                            :value="$schedule->dosage_multiplier ?? $medication->dosage ?? ''"
                            step="0.01"
                            min="0.01"
                        />

                        <x-form-field
                            name="unit"
                            label="Unit"
                            type="text"
                            :value="$schedule->unit ?? $medication->unit ?? ''"
                            placeholder="e.g., mg, ml, tablets"
                            maxlength="50"
                        />

                        <x-form-field
                            name="frequency"
                            label="Frequency"
                            type="select"
                            id="edit_frequency_{{ $schedule->id }}"
                            :options="[
                                'daily' => 'Daily',
                                'weekly' => 'Weekly',
                                'as_needed' => 'As Needed'
                            ]"
                            :value="$schedule->frequency"
                            onchange="toggleEditDaysOfWeek{{ $schedule->id }}()"
                            required
                        />

                        <div id="editDaysOfWeekContainer{{ $schedule->id }}" class="form-control {{ $schedule->frequency === 'weekly' ? '' : 'hidden' }}">
                            <label class="label">
                                <span class="label-text">Days of Week</span>
                            </label>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $index => $day)
                                    <label class="label cursor-pointer gap-2">
                                        <input type="checkbox" name="days_of_week[]" value="{{ $index }}" class="checkbox checkbox-sm" {{ $schedule->days_of_week && in_array($index, $schedule->days_of_week) ? 'checked' : '' }}>
                                        <span class="label-text">{{ $day }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <x-form-field
                            name="active"
                            label="Active"
                            type="checkbox"
                            value="1"
                            class="checkbox checkbox-primary"
                            :checked="$schedule->active"
                        />

                        <x-form-field
                            name="notes"
                            label="Notes"
                            type="textarea"
                            rows="2"
                            :value="$schedule->notes"
                            optional
                        />

                        <div class="modal-action">
                            <button type="button" class="btn btn-outline" onclick="editScheduleModal{{ $schedule->id }}.close()">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <x-heroicon-o-check class="h-4 w-4" />
                                Update Schedule
                            </button>
                        </div>
                    </form>
                </div>
                <form method="dialog" class="modal-backdrop">
                    <button>close</button>
                </form>
            </dialog>

            <script>
                function toggleEditDaysOfWeek{{ $schedule->id }}() {
                    const frequency = document.getElementById('edit_frequency_{{ $schedule->id }}').value;
                    const container = document.getElementById('editDaysOfWeekContainer{{ $schedule->id }}');

                    if (frequency === 'weekly') {
                        container.classList.remove('hidden');
                    } else {
                        container.classList.add('hidden');
                    }
                }

                function setEditScheduleTime{{ $schedule->id }}(time) {
                    document.getElementById('edit_scheduled_time_{{ $schedule->id }}').value = time;
                }
            </script>
        @endforeach
    @endif

    <script>
        function toggleDaysOfWeek() {
            const frequency = document.getElementById('frequency').value;
            const container = document.getElementById('daysOfWeekContainer');

            if (frequency === 'weekly') {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }

        function setScheduleTime(time) {
            document.getElementById('scheduled_time').value = time;
        }
    </script>
</x-layouts.app>
