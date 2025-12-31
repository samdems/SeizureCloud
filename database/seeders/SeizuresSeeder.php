<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Seizure;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeizuresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the test user or create one if it doesn't exist
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
            ],
        );

        $this->createSeizureEvents($user);
        $this->createEmergencySeizures($user);
        $this->createIsolatedSeizures($user);
        $this->createMenstrualSeizures($user);
        $this->createNighttimeSeizures($user);
    }

    /**
     * Create seizure events with clusters
     */
    private function createSeizureEvents(User $user): void
    {
        // Create 3-4 seizure events over the past 6 months
        for ($i = 0; $i < 4; $i++) {
            $eventStartDate = fake()->dateTimeBetween("-6 months", "-1 month");
            $clusterType = fake()->randomElement([
                "mild_cluster",
                "severe_cluster",
                "mixed_cluster",
            ]);

            switch ($clusterType) {
                case "mild_cluster":
                    $this->createMildCluster($user, $eventStartDate);
                    break;
                case "severe_cluster":
                    $this->createSevereCluster($user, $eventStartDate);
                    break;
                case "mixed_cluster":
                    $this->createMixedCluster($user, $eventStartDate);
                    break;
            }
        }

        // Create recent cluster event (within last month)
        $recentEventDate = fake()->dateTimeBetween("-1 month", "-1 week");
        $this->createMixedCluster($user, $recentEventDate);
    }

    /**
     * Create a mild seizure cluster
     */
    private function createMildCluster(User $user, $baseDate): void
    {
        $clusterSize = fake()->numberBetween(2, 3);
        $seizures = [];

        // First seizure
        $firstSeizure = Seizure::factory()
            ->mild()
            ->create([
                "user_id" => $user->id,
                "start_time" => $baseDate,
                "duration_minutes" => fake()->numberBetween(1, 2), // Very short
            ]);

        $seizures[] = $firstSeizure;

        // Subsequent seizures within 6 hours
        for ($i = 1; $i < $clusterSize; $i++) {
            $previousSeizure = $seizures[$i - 1];
            $minutesAfter = fake()->numberBetween(45, 360); // 45 minutes to 6 hours

            $newStartTime = (clone $previousSeizure->start_time)->modify(
                "+{$minutesAfter} minutes",
            );
            $durationMinutes = fake()->numberBetween(1, 3); // Keep very short
            $endTime = (clone $newStartTime)->modify(
                "+{$durationMinutes} minutes",
            );

            $seizure = Seizure::factory()
                ->mild()
                ->create([
                    "user_id" => $user->id,
                    "start_time" => $newStartTime,
                    "end_time" => $endTime,
                    "duration_minutes" => $durationMinutes,
                    "notes" =>
                        $i === 1
                            ? "Second seizure of the day"
                            : "Multiple seizures today",
                ]);

            $seizures[] = $seizure;
        }
    }

    /**
     * Create a severe seizure cluster
     */
    private function createSevereCluster(User $user, $baseDate): void
    {
        $clusterSize = fake()->numberBetween(2, 3); // Reduce cluster size
        $seizures = [];

        // First severe seizure - but not too long
        $firstSeizure = Seizure::factory()->create([
            "user_id" => $user->id,
            "start_time" => $baseDate,
            "duration_minutes" => fake()->numberBetween(2, 4), // Shorter
            "severity" => fake()->numberBetween(6, 8), // Reduce severity
            "notes" => "Start of cluster event",
        ]);

        $seizures[] = $firstSeizure;

        // Subsequent seizures within 8 hours
        for ($i = 1; $i < $clusterSize; $i++) {
            $previousSeizure = $seizures[$i - 1];
            $minutesAfter = fake()->numberBetween(60, 480); // 1 to 8 hours

            $newStartTime = (clone $previousSeizure->start_time)->modify(
                "+{$minutesAfter} minutes",
            );
            $durationMinutes = fake()->numberBetween(1, 4); // Much shorter
            $endTime = (clone $newStartTime)->modify(
                "+{$durationMinutes} minutes",
            );

            // NHS contact less likely
            $nhsContacted = fake()->boolean(40);

            $seizure = Seizure::factory()->create([
                "user_id" => $user->id,
                "start_time" => $newStartTime,
                "end_time" => $endTime,
                "duration_minutes" => $durationMinutes,
                "severity" => fake()->numberBetween(4, 7), // Moderate severity
                "nhs_contact_type" => $nhsContacted
                    ? fake()->randomElement([
                        "111",
                        "GP",
                        "Epileptic Specialist",
                    ])
                    : "None",
                "ambulance_called" => fake()->boolean(10), // Much less likely
                "notes" =>
                    "Seizure #" .
                    ($i + 1) .
                    " in cluster - " .
                    fake()->randomElement([
                        "Still feeling unwell from previous seizure",
                        "Postictal state from earlier seizure",
                        "Cluster pattern continuing",
                    ]),
            ]);

            $seizures[] = $seizure;
        }
    }

    /**
     * Create a mixed severity cluster
     */
    private function createMixedCluster(User $user, $baseDate): void
    {
        $clusterSize = fake()->numberBetween(2, 3); // Smaller clusters
        $seizures = [];

        // First seizure (moderate severity)
        $firstSeverity = fake()->numberBetween(3, 5);
        $firstSeizure = Seizure::factory()->create([
            "user_id" => $user->id,
            "start_time" => $baseDate,
            "severity" => $firstSeverity,
            "duration_minutes" => fake()->numberBetween(1, 3), // Short
            "notes" => "Start of seizure event",
        ]);

        $seizures[] = $firstSeizure;

        // Subsequent seizures with varying severity
        for ($i = 1; $i < $clusterSize; $i++) {
            $previousSeizure = $seizures[$i - 1];
            $minutesAfter = fake()->numberBetween(120, 720); // 2 to 12 hours

            $newStartTime = (clone $previousSeizure->start_time)->modify(
                "+{$minutesAfter} minutes",
            );
            $durationMinutes = fake()->numberBetween(1, 4); // Keep short
            $endTime = (clone $newStartTime)->modify(
                "+{$durationMinutes} minutes",
            );

            // Keep severity moderate
            $severity = fake()->numberBetween(2, 6);

            $seizure = Seizure::factory()->create([
                "user_id" => $user->id,
                "start_time" => $newStartTime,
                "end_time" => $endTime,
                "duration_minutes" => $durationMinutes,
                "severity" => $severity,
                "nhs_contact_type" =>
                    $severity >= 6
                        ? fake()->randomElement(["111", "GP"])
                        : "None",
                "notes" => "Seizure #" . ($i + 1) . " in event",
            ]);

            $seizures[] = $seizure;
        }
    }

    /**
     * Create isolated seizures (not part of clusters)
     */
    private function createIsolatedSeizures(User $user): void
    {
        // Create 8-12 isolated seizures over 6 months
        Seizure::factory()
            ->count(fake()->numberBetween(8, 12))
            ->create([
                "user_id" => $user->id,
            ]);

        // Create a few recent isolated seizures
        for ($i = 0; $i < 3; $i++) {
            Seizure::factory()->create([
                "user_id" => $user->id,
                "start_time" => fake()->dateTimeBetween("-2 weeks", "-2 days"),
            ]);
        }
    }

    /**
     * Create seizures related to menstrual cycle
     */
    private function createMenstrualSeizures(User $user): void
    {
        // Create 3-4 menstrual-related seizures over past months
        for ($i = 0; $i < 4; $i++) {
            $menstrualDate = fake()->dateTimeBetween("-6 months", "-1 week");

            Seizure::factory()
                ->menstrual()
                ->create([
                    "user_id" => $user->id,
                    "start_time" => $menstrualDate,
                    "notes" => fake()->randomElement([
                        "Seizure during menstrual cycle",
                        "Monthly pattern - menstrual seizure",
                        "Hormonal trigger suspected",
                    ]),
                ]);

            // Sometimes create a second seizure during the same menstrual period
            if (fake()->boolean(40)) {
                $secondSeizureTime = (clone $menstrualDate)->modify(
                    "+" . fake()->numberBetween(1, 3) . " days",
                );

                Seizure::factory()
                    ->menstrual()
                    ->create([
                        "user_id" => $user->id,
                        "start_time" => $secondSeizureTime,
                        "notes" => "Second seizure this cycle",
                    ]);
            }
        }
    }

    /**
     * Create nighttime seizures
     */
    private function createNighttimeSeizures(User $user): void
    {
        // Create 4-6 nighttime seizures
        Seizure::factory()
            ->nighttime()
            ->count(fake()->numberBetween(4, 6))
            ->create([
                "user_id" => $user->id,
                "notes" => fake()->randomElement([
                    "Nighttime seizure - woke up disoriented",
                    "Seizure during sleep",
                    "Found evidence of seizure in morning",
                    "Partner witnessed nighttime seizure",
                ]),
            ]);
    }

    /**
     * Create emergency seizure scenarios based on user settings
     */
    private function createEmergencySeizures(User $user): void
    {
        // Create only ONE status epilepticus seizure (rare occurrence)
        $statusEpilepticusDate = fake()->dateTimeBetween(
            "-6 months",
            "-3 months",
        );
        $emergencyDuration = $user->status_epilepticus_duration_minutes + 2; // Just 7 minutes
        $startTime = $statusEpilepticusDate;
        $endTime = (clone $startTime)->modify("+{$emergencyDuration} minutes");

        Seizure::factory()->create([
            "user_id" => $user->id,
            "start_time" => $startTime,
            "end_time" => $endTime,
            "duration_minutes" => $emergencyDuration,
            "severity" => 8,
            "nhs_contact_type" => "999",
            "ambulance_called" => true,
            "notes" =>
                "Possible Status Epilepticus - emergency duration exceeded " .
                $emergencyDuration .
                " minutes",
        ]);

        // Create a recent near-emergency scenario (just under thresholds)
        $recentDate = fake()->dateTimeBetween("-2 weeks", "-3 days");

        // Near status epilepticus (1 minute under threshold)
        $nearEmergencyDuration = 4; // Just under 5 minute threshold
        $nearStartTime = $recentDate;
        $nearEndTime = (clone $nearStartTime)->modify(
            "+{$nearEmergencyDuration} minutes",
        );

        Seizure::factory()->create([
            "user_id" => $user->id,
            "start_time" => $nearStartTime,
            "end_time" => $nearEndTime,
            "duration_minutes" => $nearEmergencyDuration,
            "severity" => fake()->numberBetween(5, 6),
            "nhs_contact_type" => fake()->boolean(50) ? "111" : "None",
            "notes" =>
                "Close to possible status epilepticus threshold - " .
                $nearEmergencyDuration .
                " minutes (threshold: " .
                $user->status_epilepticus_duration_minutes .
                " min)",
        ]);
    }
}
