<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\MedicationSchedule;
use App\Models\MedicationLog;
use Illuminate\Console\Command;
use Carbon\Carbon;

class MarkMissedMedicationsSkipped extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'medication:mark-missed-skipped
                            {--date= : Process a specific date (Y-m-d format, defaults to yesterday)}
                            {--user= : Process only for a specific user ID}
                            {--dry-run : Show what would be marked as skipped without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically mark missed scheduled medications as skipped';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $specificUserId = $this->option('user');

        // Get the date to process (defaults to yesterday)
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))->startOfDay()
            : Carbon::yesterday()->startOfDay();

        $this->info("Processing missed medications for: " . $date->format('l, F j, Y'));

        if ($isDryRun) {
            $this->warn("DRY RUN MODE - No changes will be made");
        }

        // Get all patients
        $userQuery = User::where('account_type', 'patient');

        if ($specificUserId) {
            $userQuery->where('id', $specificUserId);
        }

        $users = $userQuery->get();

        $this->info("Processing {$users->count()} patient(s)...");

        $totalMarked = 0;
        $usersProcessed = 0;

        foreach ($users as $user) {
            $markedCount = $this->processUserMedications($user, $date, $isDryRun);

            if ($markedCount > 0) {
                $usersProcessed++;
                $totalMarked += $markedCount;

                if ($isDryRun) {
                    $this->line("  [{$user->name}] Would mark {$markedCount} medication(s) as skipped");
                } else {
                    $this->line("  [{$user->name}] Marked {$markedCount} medication(s) as skipped");
                }
            }
        }

        $this->newLine();

        if ($isDryRun) {
            $this->info("DRY RUN COMPLETE");
            $this->info("Would mark {$totalMarked} missed medication(s) as skipped for {$usersProcessed} user(s)");
        } else {
            $this->info("COMPLETE");
            $this->info("Marked {$totalMarked} missed medication(s) as skipped for {$usersProcessed} user(s)");
        }

        return Command::SUCCESS;
    }

    /**
     * Process medications for a specific user
     */
    protected function processUserMedications(User $user, Carbon $date, bool $isDryRun): int
    {
        $markedCount = 0;

        // Get all active medications with their schedules
        $medications = $user->medications()
            ->where('active', true)
            ->where('as_needed', false) // Don't process as-needed medications
            ->with(['schedules' => function ($query) {
                $query->where('active', true);
            }])
            ->get();

        foreach ($medications as $medication) {
            foreach ($medication->schedules as $schedule) {
                // Check if this schedule was active on the target date
                if (!$this->wasScheduleActiveOnDate($schedule, $date)) {
                    continue;
                }

                // Check if there's already a log entry for this schedule on this date
                $existingLog = MedicationLog::where('medication_id', $medication->id)
                    ->where('medication_schedule_id', $schedule->id)
                    ->whereDate('taken_at', $date)
                    ->first();

                // If no log exists, this medication was missed
                if (!$existingLog) {
                    if (!$isDryRun) {
                        // Create a skipped log entry
                        MedicationLog::create([
                            'medication_id' => $medication->id,
                            'medication_schedule_id' => $schedule->id,
                            'taken_at' => $date->copy()->setTimeFrom($schedule->scheduled_time),
                            'intended_time' => $date->copy()->setTimeFrom($schedule->scheduled_time),
                            'dosage_taken' => null,
                            'skipped' => true,
                            'skip_reason' => 'Automatically marked as skipped (not logged)',
                            'notes' => 'Auto-marked by system at midnight',
                        ]);
                    }

                    $markedCount++;

                    if ($this->option('verbose') || $isDryRun) {
                        $scheduledTime = $schedule->scheduled_time->format('g:i A');
                        $this->line("    - {$medication->name} @ {$scheduledTime}");
                    }
                }
            }
        }

        return $markedCount;
    }

    /**
     * Check if a schedule was active on a specific date
     */
    protected function wasScheduleActiveOnDate(MedicationSchedule $schedule, Carbon $date): bool
    {
        // Check if schedule is for this day of week
        $dayOfWeek = strtolower($date->format('l'));
        $dayColumn = "{$dayOfWeek}_enabled";

        if (!$schedule->$dayColumn) {
            return false;
        }

        // Check if the schedule existed on this date
        if ($schedule->created_at && $schedule->created_at->greaterThan($date->endOfDay())) {
            return false;
        }

        return true;
    }
}
