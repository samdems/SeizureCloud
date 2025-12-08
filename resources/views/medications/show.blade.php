<x-layouts.app :title="__('Medication Details')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 max-w-6xl mx-auto">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">{{ $medication->name }}</h1>
            <div class="flex gap-2">
                <a href="{{ route('medications.edit', $medication) }}" class="btn btn-warning btn-sm">
                    Edit
                </a>
                <a href="{{ route('medications.index') }}" class="btn btn-outline btn-sm">
                    Back to List
                </a>
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
                        Log Taken Now
                    </button>
                    <button class="btn btn-primary btn-block" onclick="addScheduleModal.showModal()">
                        Add Schedule
                    </button>
                    <form action="{{ route('medications.destroy', $medication) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-error btn-block">Delete Medication</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Schedules -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <div class="flex justify-between items-center">
                    <h2 class="card-title">Schedules</h2>
                    <button class="btn btn-primary btn-sm" onclick="addScheduleModal.showModal()">Add Schedule</button>
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
                                            @if($schedule->getCalculatedDosageWithUnit())
                                                {{ $schedule->getCalculatedDosageWithUnit() }}
                                                @if($schedule->dosage_multiplier != 1)
                                                    <span class="text-xs text-base-content/60">({{ $schedule->dosage_multiplier }}x)</span>
                                                @endif
                                            @else
                                                <span class="text-base-content/50">-</span>
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
                                            <form action="{{ route('medications.schedules.destroy', [$medication, $schedule]) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-error" onclick="return confirm('Delete this schedule?')">Delete</button>
                                            </form>
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
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($medication->logs as $log)
                                    <tr>
                                        <td>{{ $log->taken_at->format('M d, Y g:i A') }}</td>
                                        <td>
                                            @if($log->skipped)
                                                <span class="badge badge-error">Skipped</span>
                                                @if($log->skip_reason)
                                                    <span class="text-xs">({{ $log->skip_reason }})</span>
                                                @endif
                                            @else
                                                <span class="badge badge-success">Taken</span>
                                            @endif
                                        </td>
                                        <td>{{ $log->dosage_taken ?? '-' }}</td>
                                        <td>{{ $log->notes ?? '-' }}</td>
                                    </tr>
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
                    <button type="submit" class="btn btn-success">Log Taken</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

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
                    label="Dosage Multiplier"
                    type="select"
                    value="1"
                    :options="[
                        '0.25' => '0.25x (¼ dose)',
                        '0.5' => '0.5x (½ dose)',
                        '0.75' => '0.75x (¾ dose)',
                        '1' => '1x (Standard dose)',
                        '1.5' => '1.5x (1½ doses)',
                        '2' => '2x (Double dose)',
                        '2.5' => '2.5x (2½ doses)',
                        '3' => '3x (Triple dose)'
                    ]"
                />
                @if($medication->dosage)
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text-alt">Base dosage: {{ $medication->dosage }} {{ $medication->unit }}</span>
                        </label>
                    </div>
                @endif

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
                    <button type="submit" class="btn btn-primary">Add Schedule</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

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
