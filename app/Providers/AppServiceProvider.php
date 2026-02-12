<?php

namespace App\Providers;

use App\Listeners\EmailLoggingListener;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        // Register email logging listeners
        Event::listen(MessageSending::class, [
            EmailLoggingListener::class,
            'handleMessageSending',
        ]);
        Event::listen(MessageSent::class, [
            EmailLoggingListener::class,
            'handleMessageSent',
        ]);

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
