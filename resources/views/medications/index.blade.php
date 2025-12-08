<x-layouts.app :title="__('Medications')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Medications</h1>
            <div class="flex gap-2">
                <a href="{{ route('medications.schedule') }}" class="btn btn-accent">
                    Today's Schedule
                </a>
                <a href="{{ route('medications.create') }}" class="btn btn-primary">
                    <x-heroicon-o-plus class="h-5 w-5" />
                    Add Medication
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Dosage</th>
                        <th>Status</th>
                        <th>Schedules</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($medications as $medication)
                        <tr class="{{ !$medication->active ? 'opacity-60' : '' }}">
                            <td>
                                <div class="font-semibold">{{ $medication->name }}</div>
                                @if($medication->description)
                                    <div class="text-sm text-base-content/70">{{ Str::limit($medication->description, 50) }}</div>
                                @endif
                            </td>
                            <td>
                                @if($medication->dosage)
                                    <span class="font-medium">{{ $medication->dosage }} {{ $medication->unit }}</span>
                                @else
                                    <span class="text-base-content/50">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($medication->active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-ghost">Inactive</span>
                                @endif
                            </td>
                            <td>
                                @if($medication->schedules->count() > 0)
                                    <div class="text-sm">
                                        @foreach($medication->schedules->take(2) as $schedule)
                                            <div>
                                                {{ Carbon\Carbon::parse($schedule->scheduled_time)->format('g:i A') }}
                                                @if($schedule->getCalculatedDosageWithUnit())
                                                    - {{ $schedule->getCalculatedDosageWithUnit() }}
                                                @endif
                                            </div>
                                        @endforeach
                                        @if($medication->schedules->count() > 2)
                                            <div class="text-base-content/50">+{{ $medication->schedules->count() - 2 }} more</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-base-content/50">No schedules</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    <a href="{{ route('medications.show', $medication) }}" class="btn btn-sm btn-info">
                                        <x-heroicon-o-eye class="h-4 w-4" />
                                        View
                                    </a>
                                    <button class="btn btn-sm btn-secondary" onclick="historyModal{{ $medication->id }}.showModal()">
                                        <x-heroicon-o-clock class="h-4 w-4" />
                                        History
                                    </button>
                                    <a href="{{ route('medications.edit', $medication) }}" class="btn btn-sm btn-warning">
                                        <x-heroicon-o-pencil class="h-4 w-4" />
                                        Edit
                                    </a>
                                    <form action="{{ route('medications.destroy', $medication) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this medication?')">
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
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                No medications added yet. <a href="{{ route('medications.create') }}" class="link link-primary">
                                    <x-heroicon-o-plus class="h-4 w-4 inline" />
                                    Add your first medication
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- History Modals for each medication -->
        @foreach($medications as $medication)
            <dialog id="historyModal{{ $medication->id }}" class="modal">
                <div class="modal-box max-w-4xl">
                    <h3 class="font-bold text-lg">{{ $medication->name }} - Recent History</h3>

                    @if($medication->logs->count() > 0)
                        <div class="overflow-x-auto mt-4">
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
                                                    <button class="btn btn-xs btn-info" onclick="editLogModal{{ $log->id }}.showModal()">
                                                        <x-heroicon-o-pencil class="h-3 w-3" />
                                                        Edit
                                                    </button>
                                                    <form action="{{ route('medications.log-destroy', $log) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this history entry?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-xs btn-error">
                                                            <x-heroicon-o-trash class="h-3 w-3" />
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
                                                <form method="POST" action="{{ route('medications.log-update', $log) }}" class="space-y-4 mt-4">
                                                    @csrf
                                                    @method('PUT')

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
                                            });
                                        </script>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 text-center">
                            <a href="{{ route('medications.show', $medication) }}" class="btn btn-primary">
                                View Full History & Details
                            </a>
                        </div>
                    @else
                        <div class="alert mt-4">
                            <span>No history logged yet for this medication.</span>
                        </div>

                        <div class="mt-4 text-center">
                            <a href="{{ route('medications.show', $medication) }}" class="btn btn-primary">
                                View Medication Details
                            </a>
                        </div>
                    @endif

                    <div class="modal-action">
                        <button type="button" class="btn" onclick="historyModal{{ $medication->id }}.close()">Close</button>
                    </div>
                </div>
                <form method="dialog" class="modal-backdrop">
                    <button>close</button>
                </form>
            </dialog>
        @endforeach
    </div>
</x-layouts.app>
