<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable("medication_reminder_logs")) {
            Schema::create("medication_reminder_logs", function (
                Blueprint $table,
            ) {
                $table->id();
                $table
                    ->foreignId("user_id")
                    ->constrained()
                    ->onDelete("cascade");
                $table
                    ->foreignId("medication_id")
                    ->constrained()
                    ->onDelete("cascade");
                $table
                    ->foreignId("medication_schedule_id")
                    ->constrained()
                    ->onDelete("cascade");
                $table->string("reminder_type")->default("overdue"); // 'due', 'overdue', 'both'
                $table->timestamp("sent_at");
                $table->string("recipient_email");
                $table->string("recipient_type")->default("patient"); // 'patient', 'trusted_contact'
                $table
                    ->foreignId("recipient_user_id")
                    ->nullable()
                    ->constrained("users")
                    ->onDelete("cascade");
                $table->integer("overdue_count")->default(0); // How many medications were overdue in this reminder
                $table->integer("due_count")->default(0); // How many medications were due in this reminder
                $table->json("medication_data")->nullable(); // Store medication details for audit trail
                $table->string("notification_id")->nullable(); // Laravel notification ID if applicable
                $table->timestamps();

                // Indexes for performance
                $table->index(
                    ["user_id", "medication_schedule_id", "sent_at"],
                    "med_reminder_user_sched_sent",
                );
                $table->index(["user_id", "sent_at"], "med_reminder_user_sent");
                $table->index(
                    ["medication_schedule_id", "sent_at"],
                    "med_reminder_sched_sent",
                );
                $table->index(["sent_at"], "med_reminder_sent_at");

                // Composite index for spam prevention queries
                $table->index(
                    ["medication_schedule_id", "reminder_type", "sent_at"],
                    "med_reminder_spam_prevention",
                );
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("medication_reminder_logs");
    }
};
