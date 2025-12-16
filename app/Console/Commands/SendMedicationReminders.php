<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\MedicationSchedule;
use App\Models\MedicationLog;
use App\Notifications\MedicationReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
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
                            {--dry-run : Show what would be sent without actually sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders for overdue or due medications to patients and their trusted contacts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for medication reminders...');

        $userQuery = User::where('account_type', 'patient')
                        ->where('email_verified_at', '!=', null);

        // Filter by specific user if provided
        if ($userId = $this->option('user')) {
            $userQuery->where('id', $userId);
        }

        $patients = $userQuery->get();

        if ($patients->isEmpty()) {
            $this->warn('No patients found to check for medication reminders.');
            return;
        }

        $totalReminders = 0;
        $usersWithReminders = 0;

        foreach ($patients as $patient) {
            $remindersSent = $this->processPatientMedications($patient);
            if ($remindersSent > 0) {
                $usersWithReminders++;
                $totalReminders += $remindersSent;
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
        // Get all active medications with schedules for today
        $medicationSchedules = MedicationSchedule::whereHas('medication', function ($query) use ($patient) {
            $query->where('user_id', $patient->id)
                  ->where('active', true)
                  ->where(function ($query) {
                      $query->whereNull('end_date')
                            ->orWhere('end_date', '>=', today());
                  });
        })
        ->where('active', true)
        ->get()
        ->filter(function ($schedule) {
            return $schedule->isScheduledForToday();
        });

        if ($medicationSchedules->isEmpty()) {
            return 0;
        }

        $overdueMedications = collect();
        $dueMedications = collect();

        foreach ($medicationSchedules as $schedule) {
            // Check if this medication has already been taken today
            $takenToday = $this->hasMedicationBeenTakenToday($schedule);

            if ($takenToday) {
                continue; // Skip if already taken
            }

            if ($schedule->isOverdue()) {
                $overdueMedications->push($schedule);
            } elseif ($schedule->isDue()) {
                $dueMedications->push($schedule);
            }
        }

        // Determine what to send based on options
        $shouldSendOverdue = !$this->option('due-only') && $overdueMedications->isNotEmpty();
        $shouldSendDue = !$this->option('overdue-only') && $dueMedications->isNotEmpty();

        if (!$shouldSendOverdue && !$shouldSendDue) {
            return 0;
        }

        // Determine reminder type
        $reminderType = 'both';
        if ($shouldSendOverdue && !$shouldSendDue) {
            $reminderType = 'overdue';
        } elseif ($shouldSendDue && !$shouldSendOverdue) {
            $reminderType = 'due';
        }

        $medicationsToInclude = collect();
        $overdueToInclude = $shouldSendOverdue ? $overdueMedications : collect();
        $dueToInclude = $shouldSendDue ? $dueMedications : collect();

        if ($this->option('dry-run')) {
            $this->displayDryRun($patient, $overdueToInclude, $dueToInclude);
            return 1; // Count as one reminder for dry run
        }

        return $this->sendReminders($patient, $overdueToInclude->toArray(), $dueToInclude->toArray(), $reminderType);
    }

    /**
     * Check if a medication has been taken today
     */
    private function hasMedicationBeenTakenToday(MedicationSchedule $schedule): bool
    {
        return MedicationLog::where('medication_id', $schedule->medication_id)
            ->where('medication_schedule_id', $schedule->id)
            ->whereDate('taken_at', today())
            ->where('skipped', false)
            ->exists();
    }

    /**
     * Send reminders to patient and trusted contacts
     */
    private function sendReminders(User $patient, array $overdueMedications, array $dueMedications, string $reminderType): int
    {
        $remindersSent = 0;

        // Send to patient
        if ($patient->email) {
            $patient->notify(new MedicationReminderNotification(
                $patient,
                $overdueMedications,
                $dueMedications,
                $reminderType
            ));
            $remindersSent++;

            $this->info("Sent medication reminder to patient: {$patient->name} ({$patient->email})");
        }

        // Send to trusted contacts if enabled
        if ($patient->notify_trusted_contacts_medication) {
            $trustedContacts = $patient->trustedUsers()
                ->wherePivot('is_active', true)
                ->get();

            foreach ($trustedContacts as $trustedContact) {
                if ($trustedContact->email) {
                    $trustedContact->notify(new MedicationReminderNotification(
                        $patient,
                        $overdueMedications,
                        $dueMedications,
                        $reminderType
                    ));
                    $remindersSent++;

                    $this->info("Sent medication reminder to trusted contact: {$trustedContact->name} ({$trustedContact->email})");
                }
            }
        }

        return $remindersSent;
    }

    /**
     * Display what would be sent in dry run mode
     */
    private function displayDryRun(User $patient, Collection $overdueMedications, Collection $dueMedications): void
    {
        $this->line("DRY RUN - Would send reminder for: {$patient->name} ({$patient->email})");

        if ($overdueMedications->isNotEmpty()) {
            $this->line("  Overdue medications:");
            foreach ($overdueMedications as $schedule) {
                $medication = $schedule->medication;
                $dosage = $schedule->getCalculatedDosageWithUnit() ?: $medication->dosage . ' ' . $medication->unit;
                $scheduledTime = $schedule->scheduled_time->format('g:i A');
                $this->line("    • {$medication->name} - {$dosage} (was due at {$scheduledTime})");
            }
        }

        if ($dueMedications->isNotEmpty()) {
            $this->line("  Due medications:");
            foreach ($dueMedications as $schedule) {
                $medication = $schedule->medication;
                $dosage = $schedule->getCalculatedDosageWithUnit() ?: $medication->dosage . ' ' . $medication->unit;
                $scheduledTime = $schedule->scheduled_time->format('g:i A');
                $this->line("    • {$medication->name} - {$dosage} (due at {$scheduledTime})");
            }
        }

        // Show who would receive notifications
        $recipients = [$patient->email];
        if ($patient->notify_trusted_contacts_medication) {
            $trustedEmails = $patient->trustedUsers()
                ->wherePivot('is_active', true)
                ->pluck('email')
                ->filter()
                ->toArray();
            $recipients = array_merge($recipients, $trustedEmails);
        }

        $this->line("  Would notify: " . implode(', ', $recipients));
        $this->line('');
    }
}
