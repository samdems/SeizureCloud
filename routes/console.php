<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command("inspire", function () {
    $this->comment(Inspiring::quote());
})->purpose("Display an inspiring quote");

// Schedule medication reminders to run every 15 minutes
Schedule::command("medication:send-reminders")
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground();

// Alternative schedules (uncomment the one you prefer):
// Run every 30 minutes:
// Schedule::command('medication:send-reminders')->everyThirtyMinutes()->withoutOverlapping()->onOneServer();

// Run every hour:
// Schedule::command('medication:send-reminders')->hourly()->withoutOverlapping()->onOneServer();

// Run at specific times (e.g., 8am, 12pm, 6pm, 9pm):
// Schedule::command('medication:send-reminders')->dailyAt('08:00')->onOneServer();
// Schedule::command('medication:send-reminders')->dailyAt('12:00')->onOneServer();
// Schedule::command('medication:send-reminders')->dailyAt('18:00')->onOneServer();
// Schedule::command('medication:send-reminders')->dailyAt('21:00')->onOneServer();
