<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medication>
 */
class MedicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $medications = [
            ["name" => "Keppra", "dosage" => "500", "unit" => "mg"],
            ["name" => "Lamotrigine", "dosage" => "100", "unit" => "mg"],
            ["name" => "Depakote", "dosage" => "250", "unit" => "mg"],
            ["name" => "Diazepam", "dosage" => "5", "unit" => "mg"],
            ["name" => "Phenobarbital", "dosage" => "30", "unit" => "mg"],
            ["name" => "Topamax", "dosage" => "50", "unit" => "mg"],
            ["name" => "Vimpat", "dosage" => "200", "unit" => "mg"],
        ];

        $med = fake()->randomElement($medications);

        return [
            "user_id" => 1,
            "name" => $med["name"],
            "dosage" => $med["dosage"],
            "unit" => $med["unit"],
            "description" => fake()->optional()->sentence(),
            "prescriber" => fake()->optional()->name(),
            "start_date" => fake()
                ->optional()
                ->dateTimeBetween("-1 year", "now"),
            "end_date" => null,
            "active" => fake()->boolean(90),
            "as_needed" => fake()->boolean(20),
            "notes" => fake()->optional()->paragraph(),
        ];
    }
}
