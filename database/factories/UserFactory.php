<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => (static::$password ??= Hash::make('password')),
            'remember_token' => Str::random(10),
            'two_factor_secret' => Str::random(10),
            'two_factor_recovery_codes' => Str::random(10),
            'two_factor_confirmed_at' => now(),
            'account_type' => 'patient',
            'morning_time' => '06:00:00',
            'afternoon_time' => '12:00:00',
            'evening_time' => '18:00:00',
            'bedtime' => '22:00:00',
            // Email preference defaults (matching migration defaults)
            'email_frequency_medication' => 'immediate',
            'email_frequency_seizures' => 'immediate',
            'email_digest_enabled' => false,
            'email_digest_frequency' => 'weekly',
            'email_digest_day' => 1,
            'email_digest_time' => '08:00:00',
            'email_include_charts' => true,
            'email_include_summaries' => true,
            'email_marketing_enabled' => false,
            'email_medication_reminders' => true,
            'email_appointment_reminders' => true,
            'email_system_updates' => true,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(
            fn (array $attributes) => [
                'email_verified_at' => null,
            ],
        );
    }

    /**
     * Indicate that the model does not have two-factor authentication configured.
     */
    public function withoutTwoFactor(): static
    {
        return $this->state(
            fn (array $attributes) => [
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
            ],
        );
    }
}
