<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vital;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VitalsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the test user or create one if it doesn't exist
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
                'email_verified_at' => now(),
                'morning_time' => '08:00',
                'afternoon_time' => '12:00',
                'evening_time' => '18:00',
                'bedtime' => '22:00',
            ]
        );

        // Create a variety of vitals for the past 60 days
        $this->createDailyVitals($user);
        $this->createWeeklyVitals($user);
        $this->createRandomVitals($user);
    }

    /**
     * Create daily vitals (heart rate, weight) for consistency
     */
    private function createDailyVitals(User $user): void
    {
        // Create heart rate readings for most days
        for ($i = 60; $i >= 0; $i--) {
            $date = now()->subDays($i);

            // 80% chance of recording heart rate each day
            if (fake()->boolean(80)) {
                Vital::factory()
                    ->heartRate()
                    ->create([
                        'user_id' => $user->id,
                        'recorded_at' => $date->setTime(
                            fake()->numberBetween(7, 10), // Morning readings
                            fake()->numberBetween(0, 59)
                        ),
                    ]);
            }
        }

        // Create weekly weight measurements
        for ($i = 8; $i >= 0; $i--) {
            $date = now()->subWeeks($i);

            Vital::factory()
                ->weight()
                ->create([
                    'user_id' => $user->id,
                    'recorded_at' => $date->setTime(
                        fake()->numberBetween(6, 8), // Early morning weigh-ins
                        fake()->numberBetween(0, 30)
                    ),
                ]);
        }
    }

    /**
     * Create weekly blood pressure readings
     */
    private function createWeeklyVitals(User $user): void
    {
        for ($i = 12; $i >= 0; $i--) {
            $date = now()->subWeeks($i)->addDays(fake()->numberBetween(0, 6));

            // Create paired systolic and diastolic readings
            $recordTime = $date->setTime(
                fake()->numberBetween(8, 20),
                fake()->numberBetween(0, 59)
            );

            Vital::factory()
                ->bloodPressureSystolic()
                ->create([
                    'user_id' => $user->id,
                    'recorded_at' => $recordTime,
                    'notes' => fake()->optional(0.2)->randomElement([
                        'Feeling stressed today',
                        'After exercise',
                        'Before medication',
                        'After morning walk'
                    ]),
                ]);

            Vital::factory()
                ->bloodPressureDiastolic()
                ->create([
                    'user_id' => $user->id,
                    'recorded_at' => $recordTime,
                ]);
        }
    }

    /**
     * Create random occasional vitals
     */
    private function createRandomVitals(User $user): void
    {
        // Blood oxygen readings (less frequent)
        Vital::factory()
            ->bloodOxygen()
            ->count(15)
            ->create([
                'user_id' => $user->id,
                'recorded_at' => fake()->dateTimeBetween('-60 days', 'now'),
            ]);

        // Temperature readings (when feeling unwell)
        Vital::factory()
            ->temperature()
            ->count(8)
            ->create([
                'user_id' => $user->id,
                'recorded_at' => fake()->dateTimeBetween('-60 days', 'now'),
                'notes' => fake()->randomElement([
                    'Feeling unwell',
                    'Checking for fever',
                    'After medication',
                    'Doctor visit',
                ]),
            ]);

        // Blood sugar readings (for diabetic monitoring)
        Vital::factory()
            ->bloodSugar()
            ->count(25)
            ->create([
                'user_id' => $user->id,
                'recorded_at' => fake()->dateTimeBetween('-60 days', 'now'),
                'notes' => fake()->optional(0.4)->randomElement([
                    'Before meal',
                    'After meal',
                    'Fasting',
                    'Before exercise',
                    'After exercise',
                ]),
            ]);

        // Respiratory rate (occasional)
        Vital::factory()
            ->respiratoryRate()
            ->count(10)
            ->create([
                'user_id' => $user->id,
                'recorded_at' => fake()->dateTimeBetween('-60 days', 'now'),
            ]);

        // Some recent vitals for current monitoring
        Vital::factory()
            ->recent()
            ->count(15)
            ->create([
                'user_id' => $user->id,
            ]);
    }
}
