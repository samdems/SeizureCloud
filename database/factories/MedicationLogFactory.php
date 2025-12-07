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

        return [
            "medication_id" => \App\Models\Medication::factory(),
            "medication_schedule_id" => fake()->optional()->randomNumber(),
            "taken_at" => fake()->dateTimeBetween("-60 days", "-11 days"),
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
