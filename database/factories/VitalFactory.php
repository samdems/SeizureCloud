<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vital>
 */
class VitalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $vitalTypes = [
            'Heart Rate' => [65, 100],          // Resting heart rate
            'Blood Pressure Systolic' => [110, 140], // Systolic BP
            'Blood Pressure Diastolic' => [70, 90],  // Diastolic BP
            'Weight' => [50, 120],               // Weight in kg
            'Body Temperature' => [36.1, 37.2], // Body temp in Celsius
            'Blood Oxygen Level' => [95, 100],   // SpO2 percentage
            'Respiratory Rate' => [12, 20],      // Breaths per minute
            'Blood Sugar' => [70, 140],          // mg/dL
        ];

        $selectedType = fake()->randomKey($vitalTypes);
        $range = $vitalTypes[$selectedType];

        return [
            'user_id' => User::factory(),
            'type' => $selectedType,
            'value' => fake()->numberBetween($range[0] * 10, $range[1] * 10) / 10, // Add decimal precision
            'recorded_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'notes' => fake()->optional(0.3)->sentence(), // 30% chance of having notes
        ];
    }

    /**
     * Create vital with specific type
     */
    public function heartRate(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'Heart Rate',
            'value' => fake()->numberBetween(650, 1000) / 10, // 65.0 - 100.0
        ]);
    }

    /**
     * Create blood pressure vitals (systolic)
     */
    public function bloodPressureSystolic(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'Blood Pressure Systolic',
            'value' => fake()->numberBetween(1100, 1400) / 10, // 110.0 - 140.0
        ]);
    }

    /**
     * Create blood pressure vitals (diastolic)
     */
    public function bloodPressureDiastolic(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'Blood Pressure Diastolic',
            'value' => fake()->numberBetween(700, 900) / 10, // 70.0 - 90.0
        ]);
    }

    /**
     * Create weight vitals
     */
    public function weight(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'Weight',
            'value' => fake()->numberBetween(500, 1200) / 10, // 50.0 - 120.0
        ]);
    }

    /**
     * Create temperature vitals
     */
    public function temperature(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'Body Temperature',
            'value' => fake()->numberBetween(361, 372) / 10, // 36.1 - 37.2
        ]);
    }

    /**
     * Create blood oxygen vitals
     */
    public function bloodOxygen(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'Blood Oxygen Level',
            'value' => fake()->numberBetween(950, 1000) / 10, // 95.0 - 100.0
        ]);
    }

    /**
     * Create respiratory rate vitals
     */
    public function respiratoryRate(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'Respiratory Rate',
            'value' => fake()->numberBetween(120, 200) / 10, // 12.0 - 20.0
        ]);
    }

    /**
     * Create blood sugar vitals
     */
    public function bloodSugar(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'Blood Sugar',
            'value' => fake()->numberBetween(700, 1400) / 10, // 70.0 - 140.0
        ]);
    }

    /**
     * Create vitals with notes
     */
    public function withNotes(): static
    {
        return $this->state(fn (array $attributes) => [
            'notes' => fake()->sentence(),
        ]);
    }

    /**
     * Create vitals from recent days
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'recorded_at' => fake()->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    /**
     * Create vitals from specific date range
     */
    public function dateRange(string $startDate, string $endDate): static
    {
        return $this->state(fn (array $attributes) => [
            'recorded_at' => fake()->dateTimeBetween($startDate, $endDate),
        ]);
    }
}
