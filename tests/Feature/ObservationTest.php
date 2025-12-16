<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Observation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ObservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_observations_index(): void
    {
        $user = User::factory()->create();
        Observation::factory()
            ->count(3)
            ->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('observations.index'));

        $response->assertStatus(200);
        $response->assertViewIs('observations.index');
        $response->assertViewHas('observations');
    }

    public function test_user_can_create_observation(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('observations.create'));

        $response->assertStatus(200);
        $response->assertViewIs('observations.create');
    }

    public function test_user_can_store_observation(): void
    {
        $user = User::factory()->create();

        $observationData = [
            'title' => 'Test observation',
            'description' => 'This is a detailed description of what I observed.',
            'observed_at' => now()->format('Y-m-d\TH:i'),
        ];

        $response = $this->actingAs($user)->post(
            route('observations.store'),
            $observationData
        );

        $response->assertRedirect(route('observations.index'));
        $response->assertSessionHas('success', 'Observation recorded successfully.');

        $this->assertDatabaseHas('observations', [
            'user_id' => $user->id,
            'title' => 'Test observation',
            'description' => 'This is a detailed description of what I observed.',
        ]);
    }

    public function test_user_can_view_observation(): void
    {
        $user = User::factory()->create();
        $observation = Observation::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(
            route('observations.show', $observation)
        );

        $response->assertStatus(200);
        $response->assertViewIs('observations.show');
        $response->assertViewHas('observation', $observation);
    }

    public function test_user_can_edit_observation(): void
    {
        $user = User::factory()->create();
        $observation = Observation::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(
            route('observations.edit', $observation)
        );

        $response->assertStatus(200);
        $response->assertViewIs('observations.edit');
        $response->assertViewHas('observation', $observation);
    }

    public function test_user_can_update_observation(): void
    {
        $user = User::factory()->create();
        $observation = Observation::factory()->create(['user_id' => $user->id]);

        $updateData = [
            'title' => 'Updated observation title',
            'description' => 'Updated description of what I observed.',
            'observed_at' => now()->subHour()->format('Y-m-d\TH:i'),
        ];

        $response = $this->actingAs($user)->put(
            route('observations.update', $observation),
            $updateData
        );

        $response->assertRedirect(route('observations.index'));
        $response->assertSessionHas('success', 'Observation updated successfully.');

        $this->assertDatabaseHas('observations', [
            'id' => $observation->id,
            'title' => 'Updated observation title',
            'description' => 'Updated description of what I observed.',
        ]);
    }

    public function test_user_can_delete_observation(): void
    {
        $user = User::factory()->create();
        $observation = Observation::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(
            route('observations.destroy', $observation)
        );

        $response->assertRedirect(route('observations.index'));
        $response->assertSessionHas('success', 'Observation deleted successfully.');

        $this->assertDatabaseMissing('observations', [
            'id' => $observation->id,
        ]);
    }

    public function test_user_cannot_view_other_users_observations(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $observation = Observation::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get(
            route('observations.show', $observation)
        );

        $response->assertStatus(403);
    }

    public function test_user_cannot_edit_other_users_observations(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $observation = Observation::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get(
            route('observations.edit', $observation)
        );

        $response->assertStatus(403);
    }

    public function test_user_cannot_update_other_users_observations(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $observation = Observation::factory()->create(['user_id' => $otherUser->id]);

        $updateData = [
            'title' => 'Malicious update',
            'description' => 'This should not work.',
            'observed_at' => now()->format('Y-m-d\TH:i'),
        ];

        $response = $this->actingAs($user)->put(
            route('observations.update', $observation),
            $updateData
        );

        $response->assertStatus(403);
    }

    public function test_user_cannot_delete_other_users_observations(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $observation = Observation::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->delete(
            route('observations.destroy', $observation)
        );

        $response->assertStatus(403);
    }

    public function test_observation_requires_title(): void
    {
        $user = User::factory()->create();

        $observationData = [
            'description' => 'This is a detailed description.',
            'observed_at' => now()->format('Y-m-d\TH:i'),
        ];

        $response = $this->actingAs($user)->post(
            route('observations.store'),
            $observationData
        );

        $response->assertSessionHasErrors('title');
    }

    public function test_observation_requires_description(): void
    {
        $user = User::factory()->create();

        $observationData = [
            'title' => 'Test observation',
            'observed_at' => now()->format('Y-m-d\TH:i'),
        ];

        $response = $this->actingAs($user)->post(
            route('observations.store'),
            $observationData
        );

        $response->assertSessionHasErrors('description');
    }

    public function test_observation_requires_observed_at(): void
    {
        $user = User::factory()->create();

        $observationData = [
            'title' => 'Test observation',
            'description' => 'This is a detailed description.',
        ];

        $response = $this->actingAs($user)->post(
            route('observations.store'),
            $observationData
        );

        $response->assertSessionHasErrors('observed_at');
    }

    public function test_observed_at_cannot_be_future(): void
    {
        $user = User::factory()->create();

        $observationData = [
            'title' => 'Test observation',
            'description' => 'This is a detailed description.',
            'observed_at' => now()->addDay()->format('Y-m-d\TH:i'),
        ];

        $response = $this->actingAs($user)->post(
            route('observations.store'),
            $observationData
        );

        $response->assertSessionHasErrors('observed_at');
    }
}
