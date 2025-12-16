@props(['user' => null])

@php
    $user = $user ?? auth()->user();

    if (!$user) {
        return;
    }

    if (!$user->canTrackSeizures()) {
        return;
    }

    $latestObservation = $user->observations()->latest('observed_at')->first();
@endphp

@if($latestObservation)
    <div class="card bg-base-100 shadow-xl border-l-4 border-l-accent">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-accent/10 rounded-lg">
                        <x-heroicon-o-document-text class="w-6 h-6 text-accent" />
                    </div>
                    <div>
                        <h2 class="card-title text-xl">Latest Observation</h2>
                        <p class="text-base-content/70 text-sm">{{ $latestObservation->observed_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('observations.show', $latestObservation) }}" class="btn btn-ghost btn-sm" wire:navigate>
                        <x-heroicon-o-eye class="w-4 h-4" />
                        View
                    </a>
                    <a href="{{ route('observations.index') }}" class="btn btn-accent btn-sm">
                        <x-heroicon-o-list-bullet class="w-4 h-4" />
                        All
                    </a>
                </div>
            </div>

            <div class="space-y-3">
                <div>
                    <h3 class="font-semibold text-lg text-base-content">{{ $latestObservation->title }}</h3>
                    <div class="text-sm text-base-content/70 mb-2">
                        {{ $latestObservation->observed_at->format('l, F j, Y \a\t g:i A') }}
                    </div>
                </div>

                <div class="bg-base-200 p-4 rounded-lg">
                    <p class="text-base-content leading-relaxed">
                        {{ Str::limit($latestObservation->description, 200) }}
                        @if(strlen($latestObservation->description) > 200)
                            <a href="{{ route('observations.show', $latestObservation) }}" class="link link-accent ml-1" wire:navigate>
                                Read more...
                            </a>
                        @endif
                    </p>
                </div>
            </div>

            <div class="card-actions justify-between pt-4 border-t border-base-300">
                <div class="text-xs text-base-content/60">
                    @if($latestObservation->updated_at->ne($latestObservation->created_at))
                        Last updated {{ $latestObservation->updated_at->diffForHumans() }}
                    @else
                        Created {{ $latestObservation->created_at->diffForHumans() }}
                    @endif
                </div>
                <a href="{{ route('observations.create') }}" class="btn btn-accent btn-sm" wire:navigate>
                    <x-heroicon-o-plus class="w-4 h-4" />
                    Add New
                </a>
            </div>
        </div>
    </div>
@else
    <div class="card bg-base-100 shadow-xl border-l-4 border-l-base-300">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-base-300/50 rounded-lg">
                        <x-heroicon-o-document-text class="w-6 h-6 text-base-content/50" />
                    </div>
                    <div>
                        <h2 class="card-title text-xl">Latest Observation</h2>
                        <p class="text-base-content/70 text-sm">No observations yet</p>
                    </div>
                </div>
            </div>

            <div class="text-center py-8">
                <div class="mb-4">
                    <x-heroicon-o-document-plus class="w-16 h-16 mx-auto text-base-content/30" />
                </div>
                <h3 class="text-lg font-medium text-base-content mb-2">Start Tracking Observations</h3>
                <p class="text-base-content/70 mb-6 max-w-md mx-auto">
                    Record your daily observations, symptoms, mood changes, or any other notes that might be helpful for tracking your health.
                </p>
                <a href="{{ route('observations.create') }}" class="btn btn-accent" wire:navigate>
                    <x-heroicon-o-plus class="w-5 h-5" />
                    Create Your First Observation
                </a>
            </div>
        </div>
    </div>
@endif
