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

<div class="card bg-base-100 shadow-xl">
    <div class="card-body">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-primary/10 rounded-lg">
                    <x-heroicon-o-plus class="w-6 h-6 text-primary" />
                </div>
                <div>
                    <h2 class="card-title text-xl">Quick Actions</h2>
                    <p class="text-base-content/70 text-sm">Common actions for tracking your health</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Add Seizure -->
            <a href="{{ route('seizures.create') }}" class="btn btn-outline btn-primary flex-col h-auto p-4 hover:bg-primary hover:text-primary-content transition-all duration-200">
                <x-heroicon-o-chart-bar class="w-8 h-8 mb-2" />
                <span class="text-sm font-medium">Add Seizure</span>
            </a>

            <!-- Add Vital -->
            <a href="{{ route('vitals.create') }}" class="btn btn-outline btn-secondary flex-col h-auto p-4 hover:bg-secondary hover:text-secondary-content transition-all duration-200">
                <x-heroicon-o-heart class="w-8 h-8 mb-2" />
                <span class="text-sm font-medium">Add Vital</span>
            </a>

            <!-- Add Observation -->
            <a href="{{ route('observations.create') }}" class="btn btn-outline btn-accent flex-col h-auto p-4 hover:bg-accent hover:text-accent-content transition-all duration-200">
                <x-heroicon-o-document-text class="w-8 h-8 mb-2" />
                <span class="text-sm font-medium">Add Observation</span>
            </a>

            <!-- View Schedule -->
            <a href="{{ route('medications.schedule') }}" class="btn btn-outline btn-info flex-col h-auto p-4 hover:bg-info hover:text-info-content transition-all duration-200">
                <x-heroicon-o-calendar class="w-8 h-8 mb-2" />
                <span class="text-sm font-medium">Today's Schedule</span>
            </a>
        </div>

        <!-- Secondary Actions Row -->
        <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-base-300">
            <a href="{{ route('medications.index') }}" class="btn btn-ghost btn-sm">
                <x-heroicon-o-beaker class="w-4 h-4" />
                Manage Medications
            </a>
            <a href="{{ route('vitals.thresholds') }}" class="btn btn-ghost btn-sm">
                <x-heroicon-o-adjustments-horizontal class="w-4 h-4" />
                Vital Thresholds
            </a>
        </div>
    </div>
</div>
