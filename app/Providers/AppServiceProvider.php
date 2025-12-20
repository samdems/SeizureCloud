<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(
            \App\Models\TrustedContact::class,
            \App\Policies\TrustedContactPolicy::class,
        );
        Gate::policy(
            \App\Models\MedicationLog::class,
            \App\Policies\MedicationLogPolicy::class,
        );
        Gate::policy(
            \App\Models\VitalTypeThreshold::class,
            \App\Policies\VitalTypeThresholdPolicy::class,
        );
        Gate::policy(
            \App\Models\Observation::class,
            \App\Policies\ObservationPolicy::class,
        );
        Gate::policy(
            \App\Models\UserInvitation::class,
            \App\Policies\UserInvitationPolicy::class,
        );
    }
}
