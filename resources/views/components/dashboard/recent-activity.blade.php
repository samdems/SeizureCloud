@props(['user' => null])

@php
    $user = $user ?? auth()->user();

    if (!$user) {
        return;
    }

    if (!$user->canTrackSeizures()) {
        return;
    }
@endphp

<div class="grid gap-6 lg:grid-cols-2">
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <h2 class="card-title text-xl">Recent Seizures</h2>
                <a href="{{ route('seizures.index') }}" class="btn btn-ghost btn-sm">View All</a>
            </div>
            @php
                $recentSeizures = $user->seizures()->latest()->take(3)->get();
            @endphp
            @if($recentSeizures->count() > 0)
                <div class="space-y-3">
                    @foreach($recentSeizures as $seizure)
                        <div class="flex items-center justify-between p-3 bg-base-200 rounded-lg">
                            <div>
                                <div class="font-medium">{{ $seizure->start_time->format('M j, Y') }}</div>
                                <div class="text-sm text-base-content/70">Severity: {{ $seizure->severity }}/10</div>
                            </div>
                            <div class="text-sm text-base-content/70">
                                {{ $seizure->start_time->format('g:i A') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-base-content/70">
                    <p class="mb-2">No seizures recorded yet</p>
                    <a href="{{ route('seizures.create') }}" class="link link-primary">Add your first seizure record</a>
                </div>
            @endif
        </div>
    </div>

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <h2 class="card-title text-xl">Recent Vitals</h2>
                <a href="{{ route('vitals.index') }}" class="btn btn-ghost btn-sm">View All</a>
            </div>
            @php
                $recentVitals = $user->vitals()->latest()->take(3)->get();
            @endphp
            @if($recentVitals->count() > 0)
                <div class="space-y-3">
                    @foreach($recentVitals as $vital)
                        <div class="flex items-center justify-between p-3 bg-base-200 rounded-lg">
                            <div>
                                <div class="font-medium">{{ $vital->type }}</div>
                                <div class="text-sm text-base-content/70">{{ $vital->recorded_at->format('M j, Y g:i A') }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-primary">{{ $vital->value }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-base-content/70">
                    <p class="mb-2">No vitals recorded yet</p>
                    <a href="{{ route('vitals.create') }}" class="link link-primary">Add your first vital</a>
                </div>
            @endif
        </div>
    </div>
</div>
