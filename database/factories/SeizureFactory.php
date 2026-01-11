<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Seizure>
 */
class SeizureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = fake()->dateTimeBetween("-6 months", "now");
        $durationMinutes = fake()->numberBetween(1, 10);
        $endTime = (clone $startTime)->modify("+{$durationMinutes} minutes");
        $nhsContacted = fake()->boolean(30);
        $ambulanceCalled = fake()->boolean(10);

        return [
            "user_id" => 1,
            "start_time" => $startTime,
            "end_time" => $endTime,
            "duration_seconds" => $durationMinutes * 60,
            "severity" => fake()->numberBetween(1, 10),
            "on_period" => fake()->boolean(50),
            "nhs_contact_type" => $nhsContacted
                ? fake()->randomElement([
                    "111",
                    "GP",
                    "999",
                    "Epileptic Specialist",
                ])
                : null,
            "postictal_state_end" => fake()
                ->optional()
                ->dateTimeBetween(
                    $endTime,
                    (clone $endTime)->modify("+2 hours"),
                ),
            "ambulance_called" => $ambulanceCalled,
            "slept_after" => fake()->boolean(70),
            "notes" => fake()->optional()->paragraph(),
        ];
    }

    /**
     * Create a seizure cluster (multiple seizures within hours of each other)
     */
    public function cluster(int $count = null, string $baseTime = null): static
    {
        $clusterCount = $count ?? fake()->numberBetween(2, 4);
        $baseDateTime = $baseTime
            ? fake()->dateTimeBetween($baseTime, $baseTime)
            : fake()->dateTimeBetween("-6 months", "now");

        return $this->state(
            fn(array $attributes) => [
                "start_time" => $baseDateTime,
            ],
        );
    }

    /**
     * Create seizures with high severity
     */
    public function severe(): static
    {
        return $this->state(
            fn(array $attributes) => [
                "severity" => fake()->numberBetween(7, 10),
                "ambulance_called" => fake()->boolean(40),
                "duration_seconds" => fake()->numberBetween(3, 8) * 60, // Slightly longer but not excessive
            ],
        );
    }

    /**
     * Create seizures with mild severity
     */
    public function mild(): static
    {
        return $this->state(
            fn(array $attributes) => [
                "severity" => fake()->numberBetween(1, 4),
                "ambulance_called" => false,
                "duration_seconds" => fake()->numberBetween(1, 3) * 60, // Very short duration
            ],
        );
    }

    /**
     * Create seizures that happen at night
     */
    public function nighttime(): static
    {
        return $this->state(function (array $attributes) {
            $date = fake()->dateTimeBetween("-6 months", "now");
            $nightHour = fake()->numberBetween(22, 6);
            if ($nightHour > 12) {
                $nightHour -= 24; // Convert to previous day for hours 22-24
            }
            $date->setTime($nightHour, fake()->numberBetween(0, 59));

            return [
                "start_time" => $date,
                "slept_after" => fake()->boolean(90), // More likely to sleep after nighttime seizures
                "duration_seconds" => fake()->numberBetween(1, 4) * 60, // Short nighttime seizures
            ];
        });
    }

    /**
     * Create seizures with specific timing relative to a base seizure
     */
    public function afterSeizure(
        $baseSeizure,
        int $minMinutes = 30,
        int $maxMinutes = 480,
    ): static {
        return $this->state(function (array $attributes) use (
            $baseSeizure,
            $minMinutes,
            $maxMinutes,
        ) {
            $minutesAfter = fake()->numberBetween($minMinutes, $maxMinutes);
            $newStartTime = (clone $baseSeizure->start_time)->modify(
                "+{$minutesAfter} minutes",
            );

            return [
                "start_time" => $newStartTime,
                "duration_seconds" => fake()->numberBetween(1, 3) * 60, // Keep subsequent seizures short
            ];
        });
    }

    /**
     * Create seizures during menstrual period
     */
    public function menstrual(): static
    {
        return $this->state(
            fn(array $attributes) => [
                "on_period" => true,
                "severity" => fake()->numberBetween(4, 8), // Often more severe during menstruation
            ],
        );
    }
}
