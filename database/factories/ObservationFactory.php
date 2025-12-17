<?php

namespace Database\Factories;

use App\Models\Observation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Observation>
 */
class ObservationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Observation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraphs(2, true),
            'observed_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }

    /**
     * Indicate that the observation was made recently.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'observed_at' => $this->faker->dateTimeBetween('-3 days', 'now'),
        ]);
    }

    /**
     * Indicate that the observation has a longer description.
     */
    public function detailed(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => $this->faker->paragraphs(5, true),
        ]);
    }

    /**
     * Indicate that the observation is about a specific topic.
     */
    public function aboutTopic(string $topic): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => "Observation about {$topic}",
            'description' => "Detailed observation regarding {$topic}. " . $this->faker->paragraphs(2, true),
        ]);
    }
}
