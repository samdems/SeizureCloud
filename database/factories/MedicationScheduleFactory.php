<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicationSchedule>
 */
class MedicationScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $times = [
            "06:00",
            "08:00",
            "12:00",
            "14:00",
            "18:00",
            "20:00",
            "22:00",
        ];
        $frequencies = ["daily", "weekly", "as_needed"];
        $frequency = fake()->randomElement($frequencies);

        $medication = \App\Models\Medication::factory()->create();

        return [
            "medication_id" => $medication->id,
            "scheduled_time" => fake()->randomElement($times),
            "dosage_multiplier" => $medication->dosage
                ? fake()->randomElement([
                    floatval($medication->dosage) * 0.5,
                    floatval($medication->dosage),
                    floatval($medication->dosage) * 1.5,
                    floatval($medication->dosage) * 2,
                ])
                : fake()->randomElement([0.5, 1, 1.5, 2]),
            "unit" => $medication->unit,
            "frequency" => $frequency,
            "days_of_week" =>
                $frequency === "weekly"
                    ? fake()->randomElements(
                        [0, 1, 2, 3, 4, 5, 6],
                        fake()->numberBetween(1, 7),
                    )
                    : null,
            "active" => fake()->boolean(95),
            "notes" => fake()->optional()->sentence(),
        ];
    }
}
