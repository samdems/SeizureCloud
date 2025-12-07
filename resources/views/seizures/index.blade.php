<x-layouts.app :title="__('Seizure Tracker')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Seizure Tracker</h1>
            <div class="flex gap-2">
                <a href="{{ route('seizures.live-tracker') }}" class="btn btn-secondary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Live Tracker
                </a>
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-outline">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Export PDF
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52">
                        <li>
                            <a href="{{ route('seizures.export.monthly-pdf', ['month' => now()->month, 'year' => now()->year]) }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Summary - This Month ({{ now()->format('M Y') }})
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('seizures.export.monthly-pdf', ['month' => now()->subMonth()->month, 'year' => now()->subMonth()->year]) }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Summary - Last Month ({{ now()->subMonth()->format('M Y') }})
                            </a>
                        </li>
                        <li>
                            <hr class="my-1">
                        </li>
                        <li>
                            <a href="{{ route('seizures.export.comprehensive-pdf', ['month' => now()->month, 'year' => now()->year]) }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Comprehensive - This Month ({{ now()->format('M Y') }})
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('seizures.export.comprehensive-pdf', ['month' => now()->subMonth()->month, 'year' => now()->subMonth()->year]) }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Comprehensive - Last Month ({{ now()->subMonth()->format('M Y') }})
                            </a>
                        </li>
                        <li>
                            <hr class="my-1">
                        </li>
                        <li>
                            <a href="#" onclick="showCustomDateModal()">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Custom Date Range
                            </a>
                        </li>
                    </ul>
                </div>
                <a href="{{ route('seizures.create') }}" class="btn btn-primary">
                    Add Past Seizure
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
                                    {{ $seizure->calculated_duration ?? 'N/A' }} min
                                    @if($seizure->emergency_status['status_epilepticus'])
                                        <div class="badge badge-error badge-xs mt-1">Status Epilepticus</div>
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
                                @if($seizure->nhs_contacted)
                                    <span class="text-success">✓ {{ $seizure->nhs_contact_type }}</span>
                                @else
                                    <span class="text-base-content/50">No</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    <a href="{{ route('seizures.show', $seizure) }}" class="btn btn-sm btn-info">View</a>
                                    <a href="{{ route('seizures.export.single-pdf', $seizure) }}" class="btn btn-sm btn-secondary" title="Export PDF">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('seizures.edit', $seizure) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('seizures.destroy', $seizure) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-error">Delete</button>
                                    </form>
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
