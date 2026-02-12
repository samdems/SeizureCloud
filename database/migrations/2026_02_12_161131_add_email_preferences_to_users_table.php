<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Email frequency preferences
            $table->enum('email_frequency_medication', ['immediate', 'daily', 'weekly', 'never'])->default('immediate')->after('notify_trusted_contacts_seizures');
            $table->enum('email_frequency_seizures', ['immediate', 'daily', 'weekly', 'never'])->default('immediate')->after('email_frequency_medication');

            // Email digest settings
            $table->boolean('email_digest_enabled')->default(false)->after('email_frequency_seizures');
            $table->enum('email_digest_frequency', ['daily', 'weekly'])->default('weekly')->after('email_digest_enabled');
            $table->tinyInteger('email_digest_day')->default(1)->comment('1-7 for day of week (Monday=1)')->after('email_digest_frequency');
            $table->time('email_digest_time')->default('08:00:00')->after('email_digest_day');

            // Email format and content preferences
            $table->boolean('email_include_charts')->default(true)->after('email_digest_time');
            $table->boolean('email_include_summaries')->default(true)->after('email_include_charts');
            $table->boolean('email_marketing_enabled')->default(false)->after('email_include_summaries');

            // Email notification controls
            $table->boolean('email_medication_reminders')->default(true)->after('email_marketing_enabled');
            $table->boolean('email_appointment_reminders')->default(true)->after('email_medication_reminders');
            $table->boolean('email_system_updates')->default(true)->after('email_appointment_reminders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'email_frequency_medication',
                'email_frequency_seizures',
                'email_digest_enabled',
                'email_digest_frequency',
                'email_digest_day',
                'email_digest_time',
                'email_include_charts',
                'email_include_summaries',
                'email_marketing_enabled',
                'email_medication_reminders',
                'email_appointment_reminders',
                'email_system_updates',
            ]);
        });
    }
};
