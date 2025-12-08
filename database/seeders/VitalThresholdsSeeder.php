<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\VitalTypeThreshold;

class VitalThresholdsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();

        foreach ($users as $user) {
            // Only create defaults if the user doesn't already have thresholds set up
            if ($user->vitalTypeThresholds()->count() === 0) {
                VitalTypeThreshold::createDefaultsForUser($user->id);

                $this->command->info("Created default vital thresholds for user: {$user->name} (ID: {$user->id})");
            } else {
                $this->command->info("User {$user->name} (ID: {$user->id}) already has vital thresholds configured");
            }
        }

        $this->command->info('Vital thresholds seeding completed!');
    }
}
