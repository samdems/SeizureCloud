<x-layouts.app :title="__('Vital Details')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Vital Details</h1>
            <div class="flex gap-2">
                <a href="{{ route('vitals.edit', $vital) }}" class="btn btn-primary">Edit</a>
                <a href="{{ route('vitals.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-xl mb-4">{{ $vital->type }}</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="label">
                            <span class="label-text font-semibold">Value</span>
                        </label>
                        <div class="text-2xl font-bold text-primary">{{ $vital->value }}</div>
                    </div>

                    <div>
                        <label class="label">
                            <span class="label-text font-semibold">Recorded At</span>
                        </label>
                        <div class="text-lg">{{ $vital->recorded_at->format('M j, Y g:i A') }}</div>
                    </div>
                </div>

                @if($vital->notes)
                    <div class="mt-6">
                        <label class="label">
                            <span class="label-text font-semibold">Notes</span>
                        </label>
                        <div class="bg-base-200 p-4 rounded-lg">
                            {{ $vital->notes }}
                        </div>
                    </div>
                @endif

                <div class="card-actions justify-end mt-6">
                    <form action="{{ route('vitals.destroy', $vital) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-error" onclick="return confirm('Are you sure you want to delete this vital record?')">
                            Delete Vital
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
