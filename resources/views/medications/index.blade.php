<x-layouts.app :title="__('Medications')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <x-page-title
            title="Medications"
            :actions="[
                [
                    'href' => route('medications.schedule'),
                    'class' => 'btn-accent',
                    'icon' => 'heroicon-o-calendar',
                    'mobile_text' => 'Schedule',
                    'desktop_text' => 'Today\'s Schedule',
                ],
                [
                    'href' => route('medications.create'),
                    'class' => 'btn-primary',
                    'icon' => 'heroicon-o-plus',
                    'mobile_text' => 'Add',
                    'desktop_text' => 'Add Medication',
                ],
            ]"
        />

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
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
                                @if($medication->as_needed)
                                    <span class="badge badge-secondary ml-2">PRN</span>
                                @endif
                            </td>
                            <td>
                                @if($medication->as_needed)
                                    <span class="text-sm text-base-content/70">As needed only</span>
                                @elseif($medication->schedules->count() > 0)
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
                            <td class="px-6 py-4">
                                <div class="flex gap-4 items-center justify-center">
                                    <a href="{{ route('medications.show', $medication) }}" class="btn btn-sm btn-info" wire:navigate>
                                        <x-heroicon-o-eye class="h-4 w-4" />
                                        View
                                    </a>
                                    <x-kebab-menu
                                        :items="[
                                            [
                                                'label' => 'History',
                                                'action' => 'historyModal' . $medication->id . '.showModal()',
                                                'icon' => 'heroicon-o-clock',
                                            ],
                                            [
                                                'label' => 'Edit',
                                                'href' => route('medications.edit', $medication),
                                                'icon' => 'heroicon-o-pencil',
                                                'wire:navigate' => true,
                                            ],
                                            [
                                                'label' => 'Delete',
                                                'form' => [
                                                    'action' => route('medications.destroy', $medication),
                                                    'method' => 'DELETE',
                                                    'confirm' => 'Are you sure you want to delete this medication?',
                                                ],
                                                'icon' => 'heroicon-o-trash',
                                            ],
                                        ]"
                                    />
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

        <!-- Mobile Card View -->
        <div class="lg:hidden space-y-4">
            @forelse($medications as $medication)
                <div class="card bg-base-100 shadow-md {{ !$medication->active ? 'opacity-60' : '' }}">
                    <div class="card-body p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="font-semibold text-base">{{ $medication->name }}</div>
                                @if($medication->description)
                                    <div class="text-sm text-base-content/70">{{ Str::limit($medication->description, 50) }}</div>
                                @endif
                            </div>
                            <div class="flex gap-1">
                                @if($medication->active)
                                    <span class="badge badge-success badge-sm">Active</span>
                                @else
                                    <span class="badge badge-ghost badge-sm">Inactive</span>
                                @endif
                                @if($medication->as_needed)
                                    <span class="badge badge-secondary badge-sm">PRN</span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div>
                                <div class="text-xs text-base-content/60 uppercase tracking-wider">Dosage</div>
                                <div class="font-medium">
                                    @if($medication->dosage)
                                        {{ $medication->dosage }} {{ $medication->unit }}
                                    @else
                                        <span class="text-base-content/50">N/A</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-base-content/60 uppercase tracking-wider">Schedules</div>
                                <div class="text-sm">
                                    @if($medication->as_needed)
                                        <span class="text-base-content/70">As needed only</span>
                                    @elseif($medication->schedules->count() > 0)
                                        <div>
                                            @foreach($medication->schedules->take(2) as $schedule)
                                                <div class="text-xs">
                                                    {{ Carbon\Carbon::parse($schedule->scheduled_time)->format('g:i A') }}
                                                    @if($schedule->getCalculatedDosageWithUnit())
                                                        - {{ $schedule->getCalculatedDosageWithUnit() }}
                                                    @endif
                                                </div>
                                            @endforeach
                                            @if($medication->schedules->count() > 2)
                                                <div class="text-xs text-base-content/50">+{{ $medication->schedules->count() - 2 }} more</div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-base-content/50">No schedules</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 pt-2 border-t border-base-300">
                            <a href="{{ route('medications.show', $medication) }}" class="btn btn-sm btn-info flex-1 min-w-0" wire:navigate>
                                <x-heroicon-o-eye class="h-4 w-4" />
                                View
                            </a>
                            <button class="btn btn-sm btn-secondary" onclick="historyModal{{ $medication->id }}.showModal()" title="History">
                                <x-heroicon-o-clock class="h-4 w-4" />
                            </button>
                            <a href="{{ route('medications.edit', $medication) }}" class="btn btn-sm btn-warning" wire:navigate>
                                <x-heroicon-o-pencil class="h-4 w-4" />
                            </a>
                            <form action="{{ route('medications.destroy', $medication) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this medication?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-error">
                                    <x-heroicon-o-trash class="h-4 w-4" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <p class="text-base-content/70 mb-4">No medications added yet.</p>
                    <a href="{{ route('medications.create') }}" class="btn btn-primary">
                        <x-heroicon-o-plus class="h-5 w-5" />
                        Add your first medication
                    </a>
                </div>
            @endforelse
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
