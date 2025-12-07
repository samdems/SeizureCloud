<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\TrustedContact;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateTestTrustedContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trusted-contacts:create-test-data";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Create test users and trusted contact relationships for demonstration";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Creating test users and trusted contact relationships...");

        // Create test users if they don't exist
        $user1 = User::firstOrCreate(
            ["email" => "patient@example.com"],
            [
                "name" => "John Patient",
                "password" => Hash::make("password"),
                "status_epilepticus_duration_minutes" => 5,
                "emergency_seizure_count" => 3,
                "emergency_seizure_timeframe_hours" => 24,
                "emergency_contact_info" => "Emergency: Call Mom at 555-0123",
                "morning_time" => "08:00",
                "afternoon_time" => "13:00",
                "evening_time" => "18:00",
                "bedtime" => "22:00",
            ],
        );

        $user2 = User::firstOrCreate(
            ["email" => "caregiver@example.com"],
            [
                "name" => "Mary Caregiver",
                "password" => Hash::make("password"),
                "status_epilepticus_duration_minutes" => 5,
                "emergency_seizure_count" => 3,
                "emergency_seizure_timeframe_hours" => 24,
                "emergency_contact_info" => "Emergency: Call John at 555-0456",
                "morning_time" => "07:00",
                "afternoon_time" => "12:00",
                "evening_time" => "17:00",
                "bedtime" => "23:00",
            ],
        );

        $user3 = User::firstOrCreate(
            ["email" => "family@example.com"],
            [
                "name" => "Sarah Family",
                "password" => Hash::make("password"),
                "status_epilepticus_duration_minutes" => 10,
                "emergency_seizure_count" => 2,
                "emergency_seizure_timeframe_hours" => 12,
                "emergency_contact_info" =>
                    "Emergency: Call 911, then notify John",
                "morning_time" => "06:30",
                "afternoon_time" => "12:30",
                "evening_time" => "18:30",
                "bedtime" => "21:30",
            ],
        );

        // Create trusted contact relationships
        $relationships = [
            [
                "user" => $user1,
                "trusted_user" => $user2,
                "nickname" => "Primary Caregiver",
                "access_note" =>
                    "Mary is my primary caregiver and needs full access to manage my medications and track seizures.",
            ],
            [
                "user" => $user1,
                "trusted_user" => $user3,
                "nickname" => "Emergency Contact - Sister",
                "access_note" =>
                    "Sarah is my emergency contact and family member who helps during medical appointments.",
            ],
            [
                "user" => $user2,
                "trusted_user" => $user1,
                "nickname" => "Patient",
                "access_note" =>
                    "John granted me access to help manage his health records as his caregiver.",
            ],
            [
                "user" => $user3,
                "trusted_user" => $user1,
                "nickname" => "Brother",
                "access_note" =>
                    "Access to help John during emergencies and family support.",
            ],
        ];

        foreach ($relationships as $relationship) {
            $existingContact = TrustedContact::where(
                "user_id",
                $relationship["user"]->id,
            )
                ->where("trusted_user_id", $relationship["trusted_user"]->id)
                ->first();

            if (!$existingContact) {
                TrustedContact::create([
                    "user_id" => $relationship["user"]->id,
                    "trusted_user_id" => $relationship["trusted_user"]->id,
                    "nickname" => $relationship["nickname"],
                    "access_note" => $relationship["access_note"],
                    "is_active" => true,
                    "granted_at" => now()->subDays(rand(1, 30)),
                    "expires_at" => null, // Permanent access
                ]);

                $this->info(
                    "✓ Created trusted contact: {$relationship["trusted_user"]->name} can access {$relationship["user"]->name}'s account",
                );
            } else {
                $this->warn(
                    "- Trusted contact already exists: {$relationship["trusted_user"]->name} -> {$relationship["user"]->name}",
                );
            }
        }

        // Create one temporary access relationship
        $tempContact = TrustedContact::where("user_id", $user1->id)
            ->where("trusted_user_id", $user3->id)
            ->first();

        if ($tempContact) {
            $tempContact->update([
                "expires_at" => now()->addDays(30),
            ]);
            $this->info(
                "✓ Updated Sarah's access to expire in 30 days (temporary access example)",
            );
        }

        $this->info("");
        $this->info("Test data created successfully!");
        $this->info("");
        $this->info("Test accounts created:");
        $this->table(
            ["Email", "Password", "Name", "Role"],
            [
                [
                    "patient@example.com",
                    "password",
                    "John Patient",
                    "Patient with epilepsy",
                ],
                [
                    "caregiver@example.com",
                    "password",
                    "Mary Caregiver",
                    "Primary caregiver",
                ],
                [
                    "family@example.com",
                    "password",
                    "Sarah Family",
                    "Family emergency contact",
                ],
            ],
        );

        $this->info("");
        $this->info("Trusted Contact Relationships:");
        $this->info('• Mary (caregiver) can access John\'s account');
        $this->info(
            '• Sarah (family) can access John\'s account (expires in 30 days)',
        );
        $this->info('• John can access Mary\'s account');
        $this->info('• John can access Sarah\'s account');
        $this->info("");
        $this->info(
            "You can now test the trusted contact feature by logging in with any of these accounts!",
        );

        return Command::SUCCESS;
    }
}
