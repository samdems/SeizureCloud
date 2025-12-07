<x-layouts.app :title="__('Vitals')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Vitals Tracker</h1>
            <a href="{{ route('vitals.create') }}" class="btn btn-primary">
                Add New Vital
            </a>
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
                        <th>Type</th>
                        <th>Value</th>
                        <th>Recorded At</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vitals as $vital)
                        <tr>
                            <td>{{ $vital->type }}</td>
                            <td>{{ $vital->value }}</td>
                            <td>{{ $vital->recorded_at->format('M j, Y g:i A') }}</td>
                            <td>{{ Str::limit($vital->notes, 50) }}</td>
                            <td>
                                <div class="flex gap-2">
                                    <a href="{{ route('vitals.show', $vital) }}" class="btn btn-sm btn-info">View</a>
                                    <a href="{{ route('vitals.edit', $vital) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('vitals.destroy', $vital) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-error" onclick="return confirm('Are you sure you want to delete this vital?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8">
                                <div class="text-gray-500">
                                    <p class="mb-2">No vitals recorded yet.</p>
                                    <a href="{{ route('vitals.create') }}" class="link link-primary">Add your first vital</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vitals->hasPages())
            <div class="flex justify-center">
                {{ $vitals->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
