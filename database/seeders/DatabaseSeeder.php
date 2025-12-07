<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create or find test user
        $user = User::firstOrCreate(
            ["email" => "test@example.com"],
            [
                "name" => "Test User",
                "password" => "password",
                "email_verified_at" => now(),
                "morning_time" => "08:00",
                "afternoon_time" => "12:00",
                "evening_time" => "18:00",
                "bedtime" => "22:00",
                "status_epilepticus_duration_minutes" => 5,
                "emergency_seizure_count" => 3,
                "emergency_seizure_timeframe_hours" => 2,
                "emergency_contact_info" =>
                    "Emergency Contact: Dr. Sarah Johnson\nPhone: 01234 567890\nRelationship: Neurologist\n\nSecondary: Jane Doe (Partner)\nPhone: 07890 123456",
            ],
        );

        // Create active medications with schedules and logs
        \App\Models\Medication::factory()
            ->count(5)
            ->create(["user_id" => $user->id, "active" => true])
            ->each(function ($medication) {
                // Create 1-3 schedules for each medication (unless it's as_needed)
                if (!$medication->as_needed) {
                    \App\Models\MedicationSchedule::factory()
                        ->count(rand(1, 3))
                        ->create([
                            "medication_id" => $medication->id,
                        ]);
                }

                // Create logs for the past 10 days (realistic adherence)
                for ($i = 10; $i >= 1; $i--) {
                    $date = now()->subDays($i);

                    foreach ($medication->schedules as $schedule) {
                        // 85% chance of taking each scheduled medication
                        if (fake()->boolean(85)) {
                            \App\Models\MedicationLog::create([
                                "medication_id" => $medication->id,
                                "medication_schedule_id" => $schedule->id,
                                "taken_at" => $date
                                    ->copy()
                                    ->setTimeFromTimeString(
                                        $schedule->scheduled_time->format(
                                            "H:i",
                                        ),
                                    ),
                                "dosage_taken" => $schedule->getCalculatedDosageWithUnit(),
                                "skipped" => false,
                                "notes" => null,
                            ]);
                        } else {
                            // Log as skipped
                            \App\Models\MedicationLog::create([
                                "medication_id" => $medication->id,
                                "medication_schedule_id" => $schedule->id,
                                "taken_at" => $date
                                    ->copy()
                                    ->setTimeFromTimeString(
                                        $schedule->scheduled_time->format(
                                            "H:i",
                                        ),
                                    ),
                                "dosage_taken" => null,
                                "skipped" => true,
                                "skip_reason" => fake()->randomElement([
                                    "Forgot",
                                    "Side effects",
                                    "Ran out",
                                ]),
                                "notes" => null,
                            ]);
                        }
                    }
                }
            });

        // Create inactive medications (no longer taking)
        \App\Models\Medication::factory()
            ->count(3)
            ->create([
                "user_id" => $user->id,
                "active" => false,
                "start_date" => fake()->dateTimeBetween("-1 year", "-2 months"),
                "end_date" => fake()->dateTimeBetween("-2 months", "-1 month"),
            ])
            ->each(function ($medication) {
                // Create historical logs for inactive medications
                if ($medication->start_date && $medication->end_date) {
                    \App\Models\MedicationLog::factory()
                        ->count(rand(10, 30))
                        ->create([
                            "medication_id" => $medication->id,
                            "medication_schedule_id" => null,
                            "taken_at" => fake()->dateTimeBetween(
                                $medication->start_date,
                                $medication->end_date,
                            ),
                        ]);
                }
            });

        // Run the comprehensive seizure seeder
        $this->call(SeizuresSeeder::class);

        // Run the vitals seeder
        $this->call(VitalsSeeder::class);
    }
}
