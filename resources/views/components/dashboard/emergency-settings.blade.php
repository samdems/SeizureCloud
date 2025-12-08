@props(['user' => null])

@php
    $user = $user ?? auth()->user();

    if (!$user) {
        return;
    }

    if (!$user->canTrackSeizures()) {
        return;
    }

    $hasEmergencyContacts = !empty($user->emergency_contact_info);
    $emergencyThresholdsSet = $user->status_epilepticus_duration_minutes && $user->emergency_seizure_count;
@endphp

<div class="card bg-base-100 shadow-xl border-l-4 border-l-error">
    <div class="card-body">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-error/10 rounded-lg">
                    <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-error" />
                </div>
                <div>
                    <h2 class="card-title text-xl">Emergency Settings</h2>
                    <p class="text-base-content/70 text-sm">Your current emergency thresholds and contacts</p>
                </div>
            </div>
            <a href="{{ route('settings.emergency-settings') }}" class="btn btn-primary btn-sm">
                <x-heroicon-o-cog-6-tooth class="w-4 h-4" />
                Configure
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Status Epilepticus Threshold -->
            <div class="stat bg-base-200 rounded-lg">
                <div class="stat-figure text-error">
                    <x-heroicon-o-clock class="w-8 h-8" />
                </div>
                <div class="stat-title">Status Epilepticus</div>
                <div class="stat-value text-2xl">{{ $user->status_epilepticus_duration_minutes }}min</div>
                <div class="stat-desc">Duration threshold</div>
            </div>

            <!-- Cluster Emergency Threshold -->
            <div class="stat bg-base-200 rounded-lg">
                <div class="stat-figure text-warning">
                    <x-heroicon-o-fire class="w-8 h-8" />
                </div>
                <div class="stat-title">Cluster Emergency</div>
                <div class="stat-value text-2xl">{{ $user->emergency_seizure_count }}</div>
                <div class="stat-desc">Seizures in {{ $user->emergency_seizure_timeframe_hours }}hrs</div>
            </div>

            <!-- Emergency Contacts Status -->
            <div class="stat bg-base-200 rounded-lg">
                <div class="stat-figure {{ $hasEmergencyContacts ? 'text-success' : 'text-error' }}">
                    <x-heroicon-o-phone class="w-8 h-8" />
                </div>
                <div class="stat-title">Emergency Contacts</div>
                <div class="stat-value text-lg {{ $hasEmergencyContacts ? 'text-success' : 'text-error' }}">
                    {{ $hasEmergencyContacts ? 'Configured' : 'Not Set' }}
                </div>
                <div class="stat-desc {{ $hasEmergencyContacts ? 'text-success' : 'text-error' }}">
                    {{ $hasEmergencyContacts ? 'Emergency info ready' : 'Please configure' }}
                </div>
            </div>
        </div>

        @if(!$hasEmergencyContacts)
            <div class="alert alert-warning mt-4">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                <span>Your emergency contact information is not set up. Consider adding this important safety information.</span>
                <a href="{{ route('settings.emergency-settings') }}" class="btn btn-warning btn-sm">
                    Set Up Now
                </a>
            </div>
        @endif
    </div>
</div>
