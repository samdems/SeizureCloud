<x-layouts.app :title="__('Medications')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Medications</h1>
            <div class="flex gap-2">
                <a href="{{ route('medications.schedule') }}" class="btn btn-accent">
                    Today's Schedule
                </a>
                <a href="{{ route('medications.create') }}" class="btn btn-primary">
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
                                    <a href="{{ route('medications.show', $medication) }}" class="btn btn-sm btn-info">View</a>
                                    <a href="{{ route('medications.edit', $medication) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('medications.destroy', $medication) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this medication?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-error">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                No medications added yet. <a href="{{ route('medications.create') }}" class="link link-primary">Add your first medication</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
