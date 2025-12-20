<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserInvitation>
 */
class UserInvitationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserInvitation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'inviter_id' => User::factory(),
            'email' => fake()->unique()->safeEmail(),
            'token' => Str::random(60),
            'nickname' => fake()->optional(0.7)->randomElement([
                'Emergency Contact',
                'Primary Caregiver',
                'Family Member',
                'Spouse',
                'Parent',
                'Sibling',
                'Medical Guardian',
                'Healthcare Proxy',
                'Trusted Friend'
            ]),
            'access_note' => fake()->optional(0.6)->paragraph(),
            'expires_at' => fake()->optional(0.3)->dateTimeBetween('+1 month', '+1 year'),
            'invitation_expires_at' => now()->addDays(7),
            'accepted_at' => null,
            'accepted_user_id' => null,
            'status' => 'pending',
        ];
    }

    /**
     * Indicate that the invitation is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'accepted_at' => null,
            'accepted_user_id' => null,
            'invitation_expires_at' => now()->addDays(7),
        ]);
    }

    /**
     * Indicate that the invitation has been accepted.
     */
    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'accepted',
            'accepted_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'accepted_user_id' => User::factory(),
        ]);
    }

    /**
     * Indicate that the invitation has been cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'accepted_at' => null,
            'accepted_user_id' => null,
        ]);
    }

    /**
     * Indicate that the invitation has expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'invitation_expires_at' => fake()->dateTimeBetween('-1 month', '-1 day'),
            'accepted_at' => null,
            'accepted_user_id' => null,
        ]);
    }

    /**
     * Create an invitation that will expire soon.
     */
    public function expiringSoon(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'invitation_expires_at' => fake()->dateTimeBetween('now', '+2 days'),
        ]);
    }

    /**
     * Create an invitation with a specific email.
     */
    public function forEmail(string $email): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => $email,
        ]);
    }

    /**
     * Create an invitation from a specific inviter.
     */
    public function fromInviter(User $inviter): static
    {
        return $this->state(fn (array $attributes) => [
            'inviter_id' => $inviter->id,
        ]);
    }

    /**
     * Create an invitation with a specific expiration date.
     */
    public function expiringAt(\DateTimeInterface $date): static
    {
        return $this->state(fn (array $attributes) => [
            'invitation_expires_at' => $date,
        ]);
    }

    /**
     * Create an invitation with no nickname.
     */
    public function withoutNickname(): static
    {
        return $this->state(fn (array $attributes) => [
            'nickname' => null,
        ]);
    }

    /**
     * Create an invitation with no access note.
     */
    public function withoutAccessNote(): static
    {
        return $this->state(fn (array $attributes) => [
            'access_note' => null,
        ]);
    }

    /**
     * Create an invitation with trusted contact expiration.
     */
    public function withTrustedContactExpiration(\DateTimeInterface $date): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => $date,
        ]);
    }
}
