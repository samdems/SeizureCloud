<div class="card bg-error text-error-content shadow-xl border-l-4 border-l-error">
    <div class="card-body">
        <div class="flex flex-row items-center justify-between">
            <div class="flex flex-row items-center gap-4">
                <div class="text-6xl">🚨</div>
                <div>
                    <h2 class="card-title text-2xl">Emergency Seizure Timer</h2>
                    <p class="text-error-content/80 mt-1">Live seizure tracking with emergency alerts</p>
                </div>
            </div>
            <div>
                <a href="{{ route('seizures.live-tracker') }}" class="btn btn-error emergency-pulse btn-lg">
                    Start Live Timer
                </a>
            </div>
        </div>
        <div class="mt-4">
            <div class="alert alert-warning">
                <x-heroicon-o-information-circle class="h-5 w-5" />
                <div class="text-sm">
                    <span class="font-semibold">Emergency Tool:</span> Available to track seizures for any patient account you have access to. Automatically alerts when seizures exceed safe duration limits.
                </div>
            </div>
        </div>
    </div>
</div>
