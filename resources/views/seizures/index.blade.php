<x-layouts.app :title="__('Seizure Tracker')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <x-page-title
            title="Seizure Tracker"
            :actions="[
                [
                    'class' => 'btn-outline',
                    'mobile_text' => 'Export PDF',
                    'desktop_text' => 'Export PDF',
                    'icon' => 'heroicon-o-document',
                    'dropdown' => [
                        [
                            'text' => 'Summary - This Month (' . now()->format('M Y') . ')',
                            'href' => route('seizures.export.monthly-pdf', ['month' => now()->month, 'year' => now()->year]),
                            'icon' => 'heroicon-o-calendar',
                        ],
                        [
                            'text' => 'Summary - Last Month (' . now()->subMonth()->format('M Y') . ')',
                            'href' => route('seizures.export.monthly-pdf', ['month' => now()->subMonth()->month, 'year' => now()->subMonth()->year]),
                            'icon' => 'heroicon-o-calendar',
                        ],
                        [
                            'divider' => true,
                        ],
                        [
                            'text' => 'Comprehensive - This Month (' . now()->format('M Y') . ')',
                            'href' => route('seizures.export.comprehensive-pdf', ['month' => now()->month, 'year' => now()->year]),
                            'icon' => 'heroicon-o-document-text',
                        ],
                        [
                            'text' => 'Comprehensive - Last Month (' . now()->subMonth()->format('M Y') . ')',
                            'href' => route('seizures.export.comprehensive-pdf', ['month' => now()->subMonth()->month, 'year' => now()->subMonth()->year]),
                            'icon' => 'heroicon-o-document-text',
                        ],
                        [
                            'divider' => true,
                        ],
                        [
                            'text' => 'Custom Date Range',
                            'href' => '#',
                            'onclick' => 'showCustomDateModal()',
                            'icon' => 'heroicon-o-cog-6-tooth',
                        ],
                    ],
                ],
                [
                    'href' => route('seizures.create'),
                    'class' => 'btn-primary',
                    'icon' => 'heroicon-o-plus',
                    'mobile_text' => 'Add',
                    'desktop_text' => 'Add Past Seizure',
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
                        <th>Date/Time</th>
                        <th>Duration</th>
                        <th>Severity</th>
                        <th>Vitals</th>
                        <th>NHS Contacted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($seizures as $seizure)
                        <tr class="{{ $seizure->emergency_status['is_emergency'] ? 'bg-error/10 border-l-4 border-l-error' : '' }}">
                            <td>
                                <div>
                                    {{ $seizure->start_time->format('M d, Y H:i') }}
                                    @if($seizure->emergency_status['is_emergency'])
                                        <div class="flex items-center gap-1 mt-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-error">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                            </svg>
                                            <span class="badge badge-error badge-xs">EMERGENCY</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div>
                                    {{ $seizure->formatted_duration ?? 'N/A' }}
                                    @if($seizure->emergency_status['status_epilepticus'])
                                        <div class="badge badge-error badge-xs mt-1">Possible Status Epilepticus</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge
                                    @if($seizure->severity >= 7) badge-error
                                    @elseif($seizure->severity >= 4) badge-warning
                                    @else badge-success
                                    @endif">
                                    {{ $seizure->severity }}/10
                                </span>
                                @if($seizure->emergency_status['cluster_emergency'])
                                    <div class="badge badge-error badge-xs mt-1">Cluster</div>
                                @endif
                            </td>
                            <td>
                                @if($seizure->vitals_count > 0)
                                    <span class="badge badge-info">{{ $seizure->vitals_count }} recorded</span>
                                @else
                                    <span class="text-base-content/50">None</span>
                                @endif
                                @if($seizure->event_seizure_count > 1)
                                    <div class="mt-1">
                                        <span class="badge badge-warning badge-xs">Event: {{ $seizure->event_seizure_count }} seizures</span>
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($seizure->nhs_contact_type)
                                    <span class="text-success">✓ {{ $seizure->nhs_contact_type }}</span>
                                @else
                                    <span class="text-base-content/50">No</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-4 items-center justify-center">
                                    <a href="{{ route('seizures.show', $seizure) }}" class="btn btn-sm btn-info" wire:navigate>
                                        <x-heroicon-o-eye class="h-4 w-4" />
                                        View
                                    </a>
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
                                                'label' => 'Delete',
                                                'form' => [
                                                    'action' => route('seizures.destroy', $seizure),
                                                    'method' => 'DELETE',
                                                    'confirm' => 'Are you sure you want to delete this record?',
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
                            <td colspan="6" class="text-center">
                                No seizure records found. <a href="{{ route('seizures.create') }}" class="link link-primary">Add your first record</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="lg:hidden space-y-4">
            @forelse($seizures as $seizure)
                <div class="card bg-base-100 shadow-md {{ $seizure->emergency_status['is_emergency'] ? 'border-l-4 border-l-error bg-error/5' : '' }}">
                    <div class="card-body p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="font-semibold text-base">{{ $seizure->start_time->format('M d, Y') }}</div>
                                <div class="text-sm text-base-content/70">{{ $seizure->start_time->format('H:i') }}</div>
                            </div>
                            @if($seizure->emergency_status['is_emergency'])
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-error">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                    <span class="badge badge-error badge-xs">EMERGENCY</span>
                                </div>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div>
                                <div class="text-xs text-base-content/60 uppercase tracking-wider">Duration</div>
                                <div class="font-medium">{{ $seizure->formatted_duration ?? 'N/A' }}</div>
                                @if($seizure->emergency_status['status_epilepticus'])
                                    <div class="badge badge-error badge-xs mt-1">Possible Status Epilepticus</div>
                                @endif
                            </div>
                            <div>
                                <div class="text-xs text-base-content/60 uppercase tracking-wider">Severity</div>
                                <div>
                                    <span class="badge
                                        @if($seizure->severity >= 7) badge-error
                                        @elseif($seizure->severity >= 4) badge-warning
                                        @else badge-success
                                        @endif">
                                        {{ $seizure->severity }}/10
                                    </span>
                                    @if($seizure->emergency_status['cluster_emergency'])
                                        <div class="badge badge-error badge-xs mt-1">Cluster</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div>
                                <div class="text-xs text-base-content/60 uppercase tracking-wider">Vitals</div>
                                <div class="text-sm">
                                    @if($seizure->vitals_count > 0)
                                        <span class="badge badge-info badge-sm">{{ $seizure->vitals_count }} recorded</span>
                                    @else
                                        <span class="text-base-content/50">None</span>
                                    @endif
                                    @if($seizure->event_seizure_count > 1)
                                        <div class="mt-1">
                                            <span class="badge badge-warning badge-xs">Event: {{ $seizure->event_seizure_count }} seizures</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-base-content/60 uppercase tracking-wider">NHS Contact</div>
                                <div class="text-sm">
                                    @if($seizure->nhs_contact_type)
                                        <span class="text-success">✓ {{ $seizure->nhs_contact_type }}</span>
                                    @else
                                        <span class="text-base-content/50">No</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 pt-2 border-t border-base-300">
                            <a href="{{ route('seizures.show', $seizure) }}" class="btn btn-sm btn-info flex-1 min-w-0" wire:navigate>
                                <x-heroicon-o-eye class="h-4 w-4" />
                                View
                            </a>
                            <a href="{{ route('seizures.export.single-pdf', $seizure) }}" class="btn btn-sm btn-secondary" title="Export PDF">
                                <x-heroicon-o-document-arrow-down class="h-4 w-4" />
                            </a>
                            <a href="{{ route('seizures.edit', $seizure) }}" class="btn btn-sm btn-warning" wire:navigate>
                                <x-heroicon-o-pencil class="h-4 w-4" />
                            </a>
                            <form action="{{ route('seizures.destroy', $seizure) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this record?')">
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
                    <p class="text-base-content/70 mb-4">No seizure records found.</p>
                    <a href="{{ route('seizures.create') }}" class="btn btn-primary">
                        <x-heroicon-o-plus class="h-5 w-5" />
                        Add your first record
                    </a>
                </div>
            @endforelse
        </div>

        @if($seizures->hasPages())
            <div class="mt-4">
                {{ $seizures->links() }}
            </div>
        @endif

        <!-- Custom Date Range Modal -->
        <div id="customDateModal" class="modal">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Export Custom Date Range</h3>
                <form id="customDateForm" method="GET" action="{{ route('seizures.export.monthly-pdf') }}">
                    <div class="py-4">
                        <div class="form-control mb-4">
                            <label class="label">
                                <span class="label-text">Export Type</span>
                            </label>
                            <select id="exportType" class="select select-bordered w-full">
                                <option value="summary">Summary Report (Overview only)</option>
                                <option value="comprehensive">Comprehensive Report (Summary + Individual details)</option>
                            </select>
                        </div>
                        <div class="form-control mb-4">
                            <label class="label">
                                <span class="label-text">Month</span>
                            </label>
                            <select name="month" class="select select-bordered w-full">
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $i == now()->month ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Year</span>
                            </label>
                            <select name="year" class="select select-bordered w-full">
                                @for($year = now()->year; $year >= now()->year - 5; $year--)
                                    <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="modal-action">
                        <button type="button" class="btn" onclick="closeCustomDateModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Export PDF</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showCustomDateModal() {
            document.getElementById('customDateModal').classList.add('modal-open');
        }

        function closeCustomDateModal() {
            document.getElementById('customDateModal').classList.remove('modal-open');
        }

        // Update form action based on export type
        document.getElementById('exportType').addEventListener('change', function() {
            const form = document.getElementById('customDateForm');
            if (this.value === 'comprehensive') {
                form.action = '{{ route("seizures.export.comprehensive-pdf") }}';
            } else {
                form.action = '{{ route("seizures.export.monthly-pdf") }}';
            }
        });

        // Close modal when clicking outside
        document.getElementById('customDateModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCustomDateModal();
            }
        });
    </script>
</x-layouts.app>
