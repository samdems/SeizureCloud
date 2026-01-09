<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command("inspire", function () {
    $this->comment(Inspiring::quote());
})->purpose("Display an inspiring quote");

// Schedule medication reminders with anti-spam protection
// Default: Every 15 minutes with 2-hour cooldown between same medication reminders
$reminderFrequency = config(
    "medication_reminders.scheduling.check_frequency_minutes",
    15,
);

if (config("medication_reminders.features.enabled", true)) {
    $command = Schedule::command("medication:send-reminders")
        ->cron("*/{$reminderFrequency} * * * *") // Dynamic frequency from config
        ->withoutOverlapping()
        ->onOneServer()
        ->runInBackground()
        ->emailOutputOnFailure(
            config("medication_reminders.analytics.summary_report_email"),
        );

    // Add description showing current spam protection settings
    $cooldownHours = config(
        "medication_reminders.spam_prevention.cooldown_hours",
        2,
    );
    $maxPerDay = config(
        "medication_reminders.spam_prevention.max_reminders_per_day",
        6,
    );
    $command->description(
        "Send medication reminders (Anti-spam: {$cooldownHours}h cooldown, max {$maxPerDay}/day)",
    );
} else {
    // Medication reminders are disabled via config
    Schedule::command("medication:send-reminders --dry-run")
        ->daily()
        ->description("Medication reminders disabled in config");
}

// Alternative schedules (uncomment and modify as needed):
//
// Every 30 minutes with spam protection:
// Schedule::command('medication:send-reminders --cooldown-hours=3 --max-per-day=4')->everyThirtyMinutes();
//
// Hourly reminders:
// Schedule::command('medication:send-reminders')->hourly();
//
// Specific times only (reduces spam):
// Schedule::command('medication:send-reminders')->dailyAt('08:00');
// Schedule::command('medication:send-reminders')->dailyAt('12:00');
// Schedule::command('medication:send-reminders')->dailyAt('18:00');
// Schedule::command('medication:send-reminders')->dailyAt('21:00');
//
// Emergency mode (bypass spam protection):
// Schedule::command('medication:send-reminders --force')->everyFifteenMinutes();

// Clean up old reminder logs (runs daily at 2 AM)
Schedule::call(function () {
    \App\Models\MedicationReminderLog::cleanup(
        config("medication_reminders.spam_prevention.log_retention_days", 90),
    );
})
    ->dailyAt("02:00")
    ->name("cleanup-medication-reminder-logs");

// System health check (runs every minute)
Schedule::job(new \App\Jobs\SystemHealthCheck())
    ->everyMinute()
    ->withoutOverlapping()
    ->onOneServer()
    ->name("system-health-check");

// Video token cleanup removed - videos no longer expire

// Track scheduler runs for health monitoring
Schedule::command("scheduler:heartbeat")
    ->everyMinute()
    ->name("scheduler-heartbeat");
