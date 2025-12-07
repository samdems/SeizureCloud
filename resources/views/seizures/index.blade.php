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
    </div>
</x-layouts.app>
