<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicationLog>
 */
class MedicationLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $skipped = fake()->boolean(10);
        $takenAt = fake()->dateTimeBetween("-60 days", "-11 days");

        // For intended_time, sometimes use the same time, sometimes offset by a few minutes
        $intendedTime = fake()->optional(80)->dateTime($takenAt);
        if ($intendedTime) {
            // Add some variation - medication might be taken early or late
            $minutesOffset = fake()->numberBetween(-30, 60);
            $intendedTime = $intendedTime->modify("-{$minutesOffset} minutes");
        }

        return [
            "medication_id" => \App\Models\Medication::factory(),
            "medication_schedule_id" => fake()->optional()->randomNumber(),
            "taken_at" => $takenAt,
            "intended_time" => $intendedTime,
            "dosage_taken" => $skipped
                ? null
                : fake()->randomElement(["500 mg", "100 mg", "250 mg", "5 mg"]),
            "skipped" => $skipped,
            "skip_reason" => $skipped
                ? fake()->randomElement([
                    "Forgot",
                    "Side effects",
                    "Ran out",
                    "Felt better",
                ])
                : null,
            "notes" => fake()->optional()->sentence(),
        ];
    }
}
