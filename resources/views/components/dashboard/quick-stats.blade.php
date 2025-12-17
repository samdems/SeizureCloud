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
<div class="grid auto-rows-min gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
    <a href="{{ route('seizures.index') }}" class="card bg-primary text-primary-content hover:shadow-xl transition-all duration-300 hover:opacity-95">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="card-title text-xl mb-2">Seizure Tracker</h3>
                    <p class="opacity-80 text-sm">Track and manage seizure records</p>
                </div>
                <div class="text-3xl">📊</div>
            </div>
            <div class="mt-4 opacity-90">
                <div class="text-2xl font-bold">{{ $user->seizures()->count() }}</div>
                <div class="text-xs opacity-75">Total records</div>
            </div>
        </div>
    </a>

    <a href="{{ route('medications.schedule') }}" class="card bg-success text-success-content hover:shadow-xl transition-all duration-300 hover:opacity-95">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="card-title text-xl mb-2">Today's Schedule</h3>
                    <p class="opacity-80 text-sm">View medication schedule</p>
                </div>
                <div class="text-3xl">📅</div>
            </div>
            <div class="mt-4 opacity-90">
                <div class="text-2xl font-bold">{{ $user->medications()->where('active', true)->count() }}</div>
                <div class="text-xs opacity-75">Active medications</div>
            </div>
        </div>
    </a>

    <a href="{{ route('medications.index') }}" class="card bg-info text-info-content hover:shadow-xl transition-all duration-300 hover:opacity-95">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="card-title text-xl mb-2">Medications</h3>
                    <p class="opacity-80 text-sm">Manage your medications</p>
                </div>
                <div class="text-3xl">💊</div>
            </div>
            <div class="mt-4 opacity-90">
                <div class="text-2xl font-bold">{{ $user->medications()->count() }}</div>
                <div class="text-xs opacity-75">Total medications</div>
            </div>
        </div>
    </a>

    <a href="{{ route('vitals.index') }}" class="card bg-secondary text-secondary-content hover:shadow-xl transition-all duration-300 hover:opacity-95">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="card-title text-xl mb-2">Vitals Tracker</h3>
                    <p class="opacity-80 text-sm">Monitor your health vitals</p>
                </div>
                <div class="text-3xl">❤️</div>
            </div>
            <div class="mt-4 opacity-90">
                <div class="text-2xl font-bold">{{ $user->vitals()->count() }}</div>
                <div class="text-xs opacity-75">Vital records</div>
            </div>
        </div>
    </a>

    <a href="{{ route('observations.index') }}" class="card bg-accent text-accent-content hover:shadow-xl transition-all duration-300 hover:opacity-95">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="card-title text-xl mb-2">Observations</h3>
                    <p class="opacity-80 text-sm">Personal notes and observations</p>
                </div>
                <div class="text-3xl">📝</div>
            </div>
            <div class="mt-4 opacity-90">
                <div class="text-2xl font-bold">{{ $user->observations()->count() }}</div>
                <div class="text-xs opacity-75">Total observations</div>
            </div>
        </div>
    </a>
</div>
