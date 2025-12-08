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

<!-- Quick Stats Cards - Patient Only -->
<div class="grid auto-rows-min gap-6 md:grid-cols-2 lg:grid-cols-4">
    <a href="{{ route('seizures.index') }}" class="card bg-gradient-to-br from-primary to-secondary hover:shadow-xl transition-all duration-300 hover:opacity-95">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="card-title text-xl text-primary-content mb-2">Seizure Tracker</h3>
                    <p class="text-primary-content/80 text-sm">Track and manage seizure records</p>
                </div>
                <div class="text-3xl text-primary-content">📊</div>
            </div>
            <div class="mt-4 text-primary-content/90">
                <div class="text-2xl font-bold">{{ $user->seizures()->count() }}</div>
                <div class="text-xs opacity-75">Total records</div>
            </div>
        </div>
    </a>

    <a href="{{ route('medications.schedule') }}" class="card bg-gradient-to-br from-success to-accent hover:shadow-xl transition-all duration-300 hover:opacity-95">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="card-title text-xl text-success-content mb-2">Today's Schedule</h3>
                    <p class="text-success-content/80 text-sm">View medication schedule</p>
                </div>
                <div class="text-3xl text-success-content">📅</div>
            </div>
            <div class="mt-4 text-success-content/90">
                <div class="text-2xl font-bold">{{ $user->medications()->where('active', true)->count() }}</div>
                <div class="text-xs opacity-75">Active medications</div>
            </div>
        </div>
    </a>

    <a href="{{ route('medications.index') }}" class="card bg-gradient-to-br from-info to-primary hover:shadow-xl transition-all duration-300 hover:opacity-95">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="card-title text-xl text-info-content mb-2">Medications</h3>
                    <p class="text-info-content/80 text-sm">Manage your medications</p>
                </div>
                <div class="text-3xl text-info-content">💊</div>
            </div>
            <div class="mt-4 text-info-content/90">
                <div class="text-2xl font-bold">{{ $user->medications()->count() }}</div>
                <div class="text-xs opacity-75">Total medications</div>
            </div>
        </div>
    </a>

    <a href="{{ route('vitals.index') }}" class="card bg-gradient-to-br from-purple-500 to-purple-700 hover:shadow-xl transition-all duration-300 hover:opacity-95">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="card-title text-xl text-white mb-2">Vitals Tracker</h3>
                    <p class="text-white/80 text-sm">Monitor your health vitals</p>
                </div>
                <div class="text-3xl text-white">❤️</div>
            </div>
            <div class="mt-4 text-white/90">
                <div class="text-2xl font-bold">{{ $user->vitals()->count() }}</div>
                <div class="text-xs opacity-75">Vital records</div>
            </div>
        </div>
    </a>
</div>
