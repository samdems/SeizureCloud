<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\MedicationSchedule;
use App\Models\MedicationLog;
use App\Models\MedicationReminderLog;
use App\Notifications\MedicationReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendMedicationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'medication:send-reminders
                            {--user= : Send reminders only for a specific user ID}
                            {--overdue-only : Send reminders only for overdue medications}
                            {--due-only : Send reminders only for currently due medications}
                            {--dry-run : Show what would be sent without actually sending}
                            {--force : Bypass spam protection and cooldown periods}
                            {--cooldown-hours=2 : Hours to wait between reminders for same medication}
                            {--max-per-day=6 : Maximum reminders per medication per day}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Send email reminders for overdue or due medications to patients and their trusted contacts";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Checking for medication reminders...");

        $userQuery = User::where("account_type", "patient")->where(
            "email_verified_at",
            "!=",
            null,
        );

        // Filter by specific user if provided
        if ($userId = $this->option("user")) {
            $userQuery->where("id", $userId);
        }

        $patients = $userQuery->get();

        if ($patients->isEmpty()) {
            $this->warn("No patients found to check for medication reminders.");
            return;
        }

        $totalReminders = 0;
        $usersWithReminders = 0;

        foreach ($patients as $patient) {
            try {
                $remindersSent = $this->processPatientMedications($patient);
                if ($remindersSent > 0) {
                    $usersWithReminders++;
                    $totalReminders += $remindersSent;
                }
            } catch (\Exception $e) {
                $this->error(
                    "Error processing patient {$patient->name} (ID: {$patient->id}): " .
                        $e->getMessage(),
                );
                Log::error("Medication reminder error for patient", [
                    "patient_id" => $patient->id,
                    "patient_name" => $patient->name,
                    "error" => $e->getMessage(),
                    "file" => $e->getFile(),
                    "line" => $e->getLine(),
                ]);
                continue;
            }
        }

        $this->info("Medication reminder check complete!");
        $this->info("Users with reminders: {$usersWithReminders}");
        $this->info("Total reminders sent: {$totalReminders}");
    }

    /**
     * Process medications for a specific patient
     */
    private function processPatientMedications(User $patient): int
    {
        try {
            // Get all active medications with schedules for today
            $medicationSchedules = MedicationSchedule::with("medication")
                ->whereHas("medication", function ($query) use ($patient) {
                    $query
                        ->where("user_id", $patient->id)
                        ->where("active", true)
                        ->where(function ($query) {
                            $query
                                ->whereNull("end_date")
                                ->orWhere("end_date", ">=", today());
                        });
                })
                ->where("active", true)
                ->get()
                ->filter(function ($schedule) {
                    return $schedule->isScheduledForToday();
                });
        } catch (\Exception $e) {
            Log::error("Error fetching medication schedules", [
                "patient_id" => $patient->id,
                "error" => $e->getMessage(),
            ]);
            throw $e;
        }

        if ($medicationSchedules->isEmpty()) {
            return 0;
        }

        $overdueMedications = collect();
        $dueMedications = collect();

        foreach ($medicationSchedules as $schedule) {
            // Ensure medication relationship is loaded
            if (!$schedule->relationLoaded("medication")) {
                $schedule->load("medication");
            }

            // Check if this medication has already been taken today
            $takenToday = $this->hasMedicationBeenTakenToday($schedule);

            if ($takenToday) {
                continue; // Skip if already taken
            }

            // Check spam protection unless --force is used
            if (!$this->option("force")) {
                $cooldownHours = (int) $this->option("cooldown-hours");
                $maxPerDay = (int) $this->option("max-per-day");

                // Skip if we've reached daily limit
                if (
                    MedicationReminderLog::hasReachedDailyLimit(
                        $schedule->id,
                        $maxPerDay,
                    )
                ) {
                    if ($this->option("dry-run")) {
                        $this->line(
                            "  - Skipping {$schedule->medication->name}: Daily limit reached ({$maxPerDay} reminders)",
                        );
                    }
                    continue;
                }

                // Skip if still in cooldown period
                if (
                    !MedicationReminderLog::canSendAnyReminder(
                        $schedule->id,
                        $cooldownHours,
                    )
                ) {
                    if ($this->option("dry-run")) {
                        $lastReminder = MedicationReminderLog::getLastReminder(
                            $schedule->id,
                        );
                        $timeSince = $lastReminder
                            ? $lastReminder->sent_at->diffForHumans()
                            : "unknown";
                        $this->line(
                            "  - Skipping {$schedule->medication->name}: In cooldown (last sent {$timeSince})",
                        );
                    }
                    continue;
                }
            }

            if ($schedule->isOverdue()) {
                $overdueMedications->push($schedule);
            } elseif ($schedule->isDue()) {
                $dueMedications->push($schedule);
            }
        }

        // Determine what to send based on options
        $shouldSendOverdue =
            !$this->option("due-only") && $overdueMedications->isNotEmpty();
        $shouldSendDue =
            !$this->option("overdue-only") && $dueMedications->isNotEmpty();

        if (!$shouldSendOverdue && !$shouldSendDue) {
            return 0;
        }

        // Determine reminder type
        $reminderType = "both";
        if ($shouldSendOverdue && !$shouldSendDue) {
            $reminderType = "overdue";
        } elseif ($shouldSendDue && !$shouldSendOverdue) {
            $reminderType = "due";
        }

        $medicationsToInclude = collect();
        $overdueToInclude = $shouldSendOverdue
            ? $overdueMedications
            : collect();
        $dueToInclude = $shouldSendDue ? $dueMedications : collect();

        if ($this->option("dry-run")) {
            $this->displayDryRun($patient, $overdueToInclude, $dueToInclude);
            return 1; // Count as one reminder for dry run
        }

        return $this->sendReminders(
            $patient,
            $overdueToInclude->values()->all(),
            $dueToInclude->values()->all(),
            $reminderType,
        );
    }

    /**
     * Check if a medication has been taken today
     */
    private function hasMedicationBeenTakenToday(
        MedicationSchedule $schedule,
    ): bool {
        return MedicationLog::where("medication_id", $schedule->medication_id)
            ->where("medication_schedule_id", $schedule->id)
            ->whereDate("taken_at", today())
            ->where("skipped", false)
            ->exists();
    }

    /**
     * Send reminders to patient and trusted contacts
     */
    private function sendReminders(
        User $patient,
        array $overdueMedications,
        array $dueMedications,
        string $reminderType,
    ): int {
        $remindersSent = 0;

        // Send to patient
        if ($patient->email) {
            try {
                $patient->notify(
                    new MedicationReminderNotification(
                        $patient,
                        $overdueMedications,
                        $dueMedications,
                        $reminderType,
                    ),
                );
                $remindersSent++;

                // Log the sent reminders for spam prevention
                $this->logSentReminders(
                    $patient,
                    $overdueMedications,
                    $dueMedications,
                    $patient,
                    "patient",
                );

                $this->info(
                    "Sent medication reminder to patient: {$patient->name} ({$patient->email})",
                );

                Log::info("Medication reminder sent to patient", [
                    "patient_id" => $patient->id,
                    "patient_email" => $patient->email,
                    "overdue_count" => count($overdueMedications),
                    "due_count" => count($dueMedications),
                ]);
            } catch (\Exception $e) {
                $this->error(
                    "Failed to send reminder to patient {$patient->name}: " .
                        $e->getMessage(),
                );
                Log::error("Failed to send medication reminder to patient", [
                    "patient_id" => $patient->id,
                    "patient_email" => $patient->email,
                    "error" => $e->getMessage(),
                ]);
            }
        }

        // Send to trusted contacts if enabled
        if ($patient->notify_trusted_contacts_medication) {
            $trustedContacts = $patient
                ->trustedUsers()
                ->wherePivot("is_active", true)
                ->get();

            foreach ($trustedContacts as $trustedContact) {
                if ($trustedContact->email) {
                    try {
                        $trustedContact->notify(
                            new MedicationReminderNotification(
                                $patient,
                                $overdueMedications,
                                $dueMedications,
                                $reminderType,
                            ),
                        );
                        $remindersSent++;

                        // Log the sent reminders for spam prevention
                        $this->logSentReminders(
                            $patient,
                            $overdueMedications,
                            $dueMedications,
                            $trustedContact,
                            "trusted_contact",
                        );

                        $this->info(
                            "Sent medication reminder to trusted contact: {$trustedContact->name} ({$trustedContact->email})",
                        );

                        Log::info(
                            "Medication reminder sent to trusted contact",
                            [
                                "patient_id" => $patient->id,
                                "trusted_contact_id" => $trustedContact->id,
                                "trusted_contact_email" =>
                                    $trustedContact->email,
                            ],
                        );
                    } catch (\Exception $e) {
                        $this->error(
                            "Failed to send reminder to trusted contact {$trustedContact->name}: " .
                                $e->getMessage(),
                        );
                        Log::error(
                            "Failed to send medication reminder to trusted contact",
                            [
                                "patient_id" => $patient->id,
                                "trusted_contact_id" => $trustedContact->id,
                                "trusted_contact_email" =>
                                    $trustedContact->email,
                                "error" => $e->getMessage(),
                            ],
                        );
                    }
                }
            }
        }

        return $remindersSent;
    }

    /**
     * Log sent reminders for spam prevention tracking
     */
    private function logSentReminders(
        User $patient,
        array $overdueMedications,
        array $dueMedications,
        User $recipient,
        string $recipientType,
    ): void {
        $allMedications = array_merge($overdueMedications, $dueMedications);

        foreach ($allMedications as $schedule) {
            try {
                MedicationReminderLog::logReminder([
                    "user_id" => $patient->id,
                    "medication_id" => $schedule->medication->id,
                    "medication_schedule_id" => $schedule->id,
                    "reminder_type" => $schedule->isOverdue()
                        ? "overdue"
                        : "due",
                    "recipient_email" => $recipient->email,
                    "recipient_type" => $recipientType,
                    "recipient_user_id" => $recipient->id,
                    "overdue_count" => count($overdueMedications),
                    "due_count" => count($dueMedications),
                    "medication_data" => [
                        "name" => $schedule->medication->name,
                        "dosage" => $schedule->medication->dosage,
                        "unit" => $schedule->medication->unit,
                        "scheduled_time" => $schedule->scheduled_time->format(
                            "H:i",
                        ),
                    ],
                ]);
            } catch (\Exception $e) {
                Log::warning("Failed to log medication reminder", [
                    "patient_id" => $patient->id,
                    "schedule_id" => $schedule->id,
                    "error" => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Display what would be sent in dry run mode
     */
    private function displayDryRun(
        User $patient,
        Collection $overdueMedications,
        Collection $dueMedications,
    ): void {
        $this->line(
            "DRY RUN - Would send reminder for: {$patient->name} ({$patient->email})",
        );

        if ($overdueMedications->isNotEmpty()) {
            $this->line("  Overdue medications:");
            foreach ($overdueMedications as $schedule) {
                $medication = $schedule->medication;
                $dosage =
                    $schedule->getCalculatedDosageWithUnit() ?:
                    $medication->dosage . " " . $medication->unit;
                $scheduledTime = $schedule->scheduled_time->format("g:i A");

                // Show spam protection info
                $todayCount = MedicationReminderLog::todayReminderCount(
                    $schedule->id,
                );
                $lastReminder = MedicationReminderLog::getLastReminder(
                    $schedule->id,
                );
                $spamInfo = $lastReminder
                    ? " (sent {$todayCount} times today, last: {$lastReminder->sent_at->diffForHumans()})"
                    : " (first reminder today)";

                $this->line(
                    "    • {$medication->name} - {$dosage} (was due at {$scheduledTime}){$spamInfo}",
                );
            }
        }

        if ($dueMedications->isNotEmpty()) {
            $this->line("  Due medications:");
            foreach ($dueMedications as $schedule) {
                $medication = $schedule->medication;
                $dosage =
                    $schedule->getCalculatedDosageWithUnit() ?:
                    $medication->dosage . " " . $medication->unit;
                $scheduledTime = $schedule->scheduled_time->format("g:i A");

                // Show spam protection info
                $todayCount = MedicationReminderLog::todayReminderCount(
                    $schedule->id,
                );
                $lastReminder = MedicationReminderLog::getLastReminder(
                    $schedule->id,
                );
                $spamInfo = $lastReminder
                    ? " (sent {$todayCount} times today, last: {$lastReminder->sent_at->diffForHumans()})"
                    : " (first reminder today)";

                $this->line(
                    "    • {$medication->name} - {$dosage} (due at {$scheduledTime}){$spamInfo}",
                );
            }
        }

        // Show who would receive notifications
        $recipients = [$patient->email];
        if ($patient->notify_trusted_contacts_medication) {
            $trustedEmails = $patient
                ->trustedUsers()
                ->wherePivot("is_active", true)
                ->pluck("email")
                ->filter()
                ->toArray();
            $recipients = array_merge($recipients, $trustedEmails);
        }

        $this->line("  Would notify: " . implode(", ", $recipients));

        // Show spam protection settings
        if (!$this->option("force")) {
            $cooldownHours = (int) $this->option("cooldown-hours");
            $maxPerDay = (int) $this->option("max-per-day");
            $this->line(
                "  Spam protection: {$cooldownHours}h cooldown, max {$maxPerDay}/day",
            );
        } else {
            $this->line("  Spam protection: DISABLED (--force used)");
        }

        $this->line("");
    }
}
