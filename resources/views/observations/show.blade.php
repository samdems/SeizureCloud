<x-layouts.app :title="__('View Observation')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 max-w-4xl mx-auto">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Observation Details</h1>
            <div class="flex gap-2">
                <a href="{{ route('observations.edit', $observation) }}" class="btn btn-warning btn-sm">
                    <x-heroicon-o-pencil class="h-4 w-4" />
                    Edit
                </a>
                <a href="{{ route('observations.index') }}" class="btn btn-ghost">
                    Back to List
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <!-- Header with Title -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-base-content mb-2">{{ $observation->title }}</h2>
                    <div class="flex flex-wrap gap-4 text-sm text-base-content/70">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-calendar class="h-4 w-4" />
                            <span>{{ $observation->observed_at->format('l, F j, Y') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-clock class="h-4 w-4" />
                            <span>{{ $observation->observed_at->format('g:i A') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-base-content mb-3 flex items-center gap-2">
                        <x-heroicon-o-document-text class="h-5 w-5" />
                        Description
                    </h3>
                    <div class="bg-base-200 p-4 rounded-lg">
                        <div class="prose prose-sm max-w-none">
                            {!! nl2br(e($observation->description)) !!}
                        </div>
                    </div>
                </div>

                <!-- Metadata Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-base-300">
                    <div>
                        <h4 class="text-sm font-medium text-base-content/70 uppercase tracking-wider mb-2">Created</h4>
                        <p class="text-sm">{{ $observation->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                    @if($observation->updated_at->ne($observation->created_at))
                        <div>
                            <h4 class="text-sm font-medium text-base-content/70 uppercase tracking-wider mb-2">Last Updated</h4>
                            <p class="text-sm">{{ $observation->updated_at->format('M j, Y g:i A') }}</p>
                        </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="card-actions justify-end pt-6 border-t border-base-300 mt-6">
                    <a href="{{ route('observations.index') }}" class="btn btn-ghost">
                        <x-heroicon-o-arrow-left class="h-4 w-4" />
                        Back to List
                    </a>
                    <a href="{{ route('observations.edit', $observation) }}" class="btn btn-warning">
                        <x-heroicon-o-pencil class="h-4 w-4" />
                        Edit
                    </a>
                    <form action="{{ route('observations.destroy', $observation) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this observation? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-error">
                            <x-heroicon-o-trash class="h-4 w-4" />
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
