<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Medication Reminder Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the automated medication
    | reminder system including spam prevention, timing, and notification
    | preferences.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Spam Prevention Settings
    |--------------------------------------------------------------------------
    |
    | These settings help prevent users from being overwhelmed with too many
    | reminder emails for the same medication.
    |
    */
    'spam_prevention' => [
        // Hours to wait between sending reminders for the same medication
        'cooldown_hours' => env('MEDICATION_REMINDER_COOLDOWN_HOURS', 2),

        // Maximum number of reminders per medication per day
        'max_reminders_per_day' => env('MEDICATION_REMINDER_MAX_PER_DAY', 6),

        // Whether spam prevention is enabled by default
        'enabled' => env('MEDICATION_REMINDER_SPAM_PREVENTION', true),

        // Days to keep reminder logs for spam prevention (also affects analytics)
        'log_retention_days' => env('MEDICATION_REMINDER_LOG_RETENTION_DAYS', 90),
    ],

    /*
    |--------------------------------------------------------------------------
    | Timing Configuration
    |--------------------------------------------------------------------------
    |
    | Configure when medications are considered due and overdue, and when
    | reminders should be sent.
    |
    */
    'timing' => [
        // Minutes of grace period before a medication is considered "overdue"
        'overdue_grace_period_minutes' => env('MEDICATION_OVERDUE_GRACE_MINUTES', 30),

        // Hours during which reminders can be sent (24-hour format)
        'quiet_hours' => [
            'start' => env('MEDICATION_REMINDER_QUIET_START', '22:00'),
            'end' => env('MEDICATION_REMINDER_QUIET_END', '07:00'),
        ],

        // Whether to respect quiet hours
        'respect_quiet_hours' => env('MEDICATION_REMINDER_RESPECT_QUIET_HOURS', true),

        // Skip reminders on weekends for weekly medications
        'skip_weekend_reminders' => env('MEDICATION_REMINDER_SKIP_WEEKENDS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Scheduling Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how often the reminder check runs and related scheduling
    | options.
    |
    */
    'scheduling' => [
        // How often to check for medication reminders (in minutes)
        // This should match your cron schedule in routes/console.php
        'check_frequency_minutes' => env('MEDICATION_REMINDER_CHECK_FREQUENCY', 15),

        // Maximum execution time for the reminder command (in seconds)
        'max_execution_time' => env('MEDICATION_REMINDER_MAX_EXECUTION_TIME', 300),

        // Whether to run reminders in background
        'run_in_background' => env('MEDICATION_REMINDER_BACKGROUND', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    |
    | Configure email notification preferences and content.
    |
    */
    'notifications' => [
        // Whether to queue notifications for better performance
        'use_queue' => env('MEDICATION_REMINDER_USE_QUEUE', true),

        // Queue connection to use for notifications
        'queue_connection' => env('MEDICATION_REMINDER_QUEUE_CONNECTION', 'database'),

        // From email address for medication reminders
        'from_email' => env('MEDICATION_REMINDER_FROM_EMAIL', env('MAIL_FROM_ADDRESS')),

        // From name for medication reminders
        'from_name' => env('MEDICATION_REMINDER_FROM_NAME', env('MAIL_FROM_NAME', 'EpiCare Medication Reminders')),

        // Whether to include medication dosage in email subjects
        'include_dosage_in_subject' => env('MEDICATION_REMINDER_DOSAGE_IN_SUBJECT', false),

        // Maximum number of medications to list in email before summarizing
        'max_medications_in_email' => env('MEDICATION_REMINDER_MAX_IN_EMAIL', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | User Preferences
    |--------------------------------------------------------------------------
    |
    | Default settings for user notification preferences.
    |
    */
    'user_defaults' => [
        // Default setting for trusted contact medication notifications
        'notify_trusted_contacts_medication' => true,

        // Whether users can disable medication reminders entirely
        'allow_user_disable' => env('MEDICATION_REMINDER_ALLOW_USER_DISABLE', true),

        // Whether users can customize reminder frequency
        'allow_user_custom_frequency' => env('MEDICATION_REMINDER_ALLOW_USER_CUSTOM_FREQUENCY', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Progressive Reminder Settings
    |--------------------------------------------------------------------------
    |
    | Configure escalating reminder behavior for medications that remain
    | overdue for extended periods.
    |
    */
    'progressive_reminders' => [
        // Whether to enable progressive (escalating) reminders
        'enabled' => env('MEDICATION_REMINDER_PROGRESSIVE', false),

        // Reminder schedule: hours after due time => reminder type
        'schedule' => [
            0 => 'due',           // Immediate reminder when due
            0.5 => 'overdue',     // 30 minutes after due time
            4 => 'urgent',        // 4 hours after due time
            24 => 'critical',     // 24 hours after due time
        ],

        // Whether to notify trusted contacts for urgent/critical reminders
        'escalate_to_trusted_contacts' => true,

        // Whether to include emergency contact info in critical reminders
        'include_emergency_contacts' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Analytics and Reporting
    |--------------------------------------------------------------------------
    |
    | Settings for tracking reminder effectiveness and user engagement.
    |
    */
    'analytics' => [
        // Whether to track reminder analytics
        'enabled' => env('MEDICATION_REMINDER_ANALYTICS', true),

        // Whether to track email opens (requires email service support)
        'track_opens' => env('MEDICATION_REMINDER_TRACK_OPENS', false),

        // Whether to track email clicks
        'track_clicks' => env('MEDICATION_REMINDER_TRACK_CLICKS', false),

        // Generate daily summary reports for administrators
        'daily_summary_reports' => env('MEDICATION_REMINDER_DAILY_REPORTS', false),

        // Email address for daily summary reports
        'summary_report_email' => env('MEDICATION_REMINDER_REPORT_EMAIL', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Emergency Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for critical medication reminders and emergency situations.
    |
    */
    'emergency' => [
        // Hours after which a missed critical medication becomes an emergency
        'critical_medication_hours' => env('MEDICATION_CRITICAL_HOURS', 48),

        // Medications that are considered critical (by name or medication ID)
        'critical_medications' => [
            // Add medication names or IDs that require special handling
            // 'Seizure Control Medication',
            // 'Anti-Epileptic Drug',
        ],

        // Whether to bypass quiet hours for emergency reminders
        'bypass_quiet_hours' => true,

        // Whether to bypass spam prevention for emergency reminders
        'bypass_spam_prevention' => true,

        // Additional email addresses to notify for medication emergencies
        'emergency_notification_emails' => env('MEDICATION_EMERGENCY_EMAILS', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Development and Testing
    |--------------------------------------------------------------------------
    |
    | Settings for development, testing, and debugging.
    |
    */
    'development' => [
        // Whether to enable debug logging for medication reminders
        'debug_logging' => env('MEDICATION_REMINDER_DEBUG', false),

        // Email address to redirect all reminders to in testing
        'test_email_override' => env('MEDICATION_REMINDER_TEST_EMAIL', null),

        // Whether to create test data when none exists
        'auto_create_test_data' => env('MEDICATION_REMINDER_AUTO_TEST_DATA', false),

        // Dry run mode - log what would be sent but don't actually send
        'dry_run_mode' => env('MEDICATION_REMINDER_DRY_RUN', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific features of the reminder system.
    |
    */
    'features' => [
        // Master switch for all medication reminders
        'enabled' => env('MEDICATION_REMINDERS_ENABLED', true),

        // Enable SMS reminders (requires SMS service integration)
        'sms_reminders' => env('MEDICATION_REMINDER_SMS', false),

        // Enable push notifications (requires mobile app integration)
        'push_notifications' => env('MEDICATION_REMINDER_PUSH', false),

        // Enable voice call reminders (requires voice service integration)
        'voice_reminders' => env('MEDICATION_REMINDER_VOICE', false),

        // Enable AI-powered reminder optimization
        'ai_optimization' => env('MEDICATION_REMINDER_AI', false),
    ],
];
