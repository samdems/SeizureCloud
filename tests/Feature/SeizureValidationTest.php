<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeizureValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_duration_minutes_validation_accepts_valid_values(): void
    {
        $user = User::factory()->create();

        $validValues = [0, 1, 30, 100, 999];

        foreach ($validValues as $value) {
            $seizureData = [
                "start_time" => now()->subHours(2),
                "end_time" => now()->subHours(1),
                "duration_minutes" => $value,
                "duration_seconds" => 0,
                "severity" => 7,
            ];

            $response = $this->actingAs($user)->postWithCsrf(
                route("seizures.store"),
                $seizureData,
            );

            $response->assertRedirect(route("seizures.index"));
            $response->assertSessionHasNoErrors();
        }
    }

    public function test_duration_minutes_validation_rejects_too_high(): void
    {
        $user = User::factory()->create();

        $seizureData = [
            "start_time" => now()->subHours(2),
            "end_time" => now()->subHours(1),
            "duration_minutes" => 1000,
            "duration_seconds" => 0,
            "severity" => 7,
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("seizures.store"),
            $seizureData,
        );

        $response->assertSessionHasErrors("duration_minutes");
        $this->assertStringContainsString(
            "cannot exceed 999",
            $response->getSession()->get("errors")->first("duration_minutes"),
        );
    }

    public function test_duration_minutes_validation_rejects_negative(): void
    {
        $user = User::factory()->create();

        $seizureData = [
            "start_time" => now()->subHours(2),
            "end_time" => now()->subHours(1),
            "duration_minutes" => -5,
            "duration_seconds" => 0,
            "severity" => 7,
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("seizures.store"),
            $seizureData,
        );

        $response->assertSessionHasErrors("duration_minutes");
        $this->assertStringContainsString(
            "cannot be negative",
            $response->getSession()->get("errors")->first("duration_minutes"),
        );
    }

    public function test_duration_minutes_validation_rejects_non_integer(): void
    {
        $user = User::factory()->create();

        $seizureData = [
            "start_time" => now()->subHours(2),
            "end_time" => now()->subHours(1),
            "duration_minutes" => "five",
            "duration_seconds" => 0,
            "severity" => 7,
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("seizures.store"),
            $seizureData,
        );

        $response->assertSessionHasErrors("duration_minutes");
        $this->assertStringContainsString(
            "must be a whole number",
            $response->getSession()->get("errors")->first("duration_minutes"),
        );
    }

    public function test_duration_seconds_validation_accepts_valid_values(): void
    {
        $user = User::factory()->create();

        $validValues = [0, 1, 15, 30, 45, 59];

        foreach ($validValues as $value) {
            $seizureData = [
                "start_time" => now()->subHours(2),
                "end_time" => now()->subHours(1),
                "duration_minutes" => 5,
                "duration_seconds" => $value,
                "severity" => 7,
            ];

            $response = $this->actingAs($user)->postWithCsrf(
                route("seizures.store"),
                $seizureData,
            );

            $response->assertRedirect(route("seizures.index"));
            $response->assertSessionHasNoErrors();
        }
    }

    public function test_duration_seconds_validation_rejects_too_high(): void
    {
        $user = User::factory()->create();

        $seizureData = [
            "start_time" => now()->subHours(2),
            "end_time" => now()->subHours(1),
            "duration_minutes" => 5,
            "duration_seconds" => 60,
            "severity" => 7,
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("seizures.store"),
            $seizureData,
        );

        $response->assertSessionHasErrors("duration_seconds");
        $this->assertStringContainsString(
            "cannot exceed 59",
            $response->getSession()->get("errors")->first("duration_seconds"),
        );
    }

    public function test_duration_seconds_validation_rejects_negative(): void
    {
        $user = User::factory()->create();

        $seizureData = [
            "start_time" => now()->subHours(2),
            "end_time" => now()->subHours(1),
            "duration_minutes" => 5,
            "duration_seconds" => -10,
            "severity" => 7,
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("seizures.store"),
            $seizureData,
        );

        $response->assertSessionHasErrors("duration_seconds");
        $this->assertStringContainsString(
            "cannot be negative",
            $response->getSession()->get("errors")->first("duration_seconds"),
        );
    }

    public function test_duration_seconds_validation_rejects_non_integer(): void
    {
        $user = User::factory()->create();

        $seizureData = [
            "start_time" => now()->subHours(2),
            "end_time" => now()->subHours(1),
            "duration_minutes" => 5,
            "duration_seconds" => "thirty",
            "severity" => 7,
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("seizures.store"),
            $seizureData,
        );

        $response->assertSessionHasErrors("duration_seconds");
        $this->assertStringContainsString(
            "must be a whole number",
            $response->getSession()->get("errors")->first("duration_seconds"),
        );
    }

    public function test_duration_fields_are_optional(): void
    {
        $user = User::factory()->create();

        $seizureData = [
            "start_time" => now()->subHours(2),
            "end_time" => now()->subHours(1),
            "severity" => 7,
            // No duration fields
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("seizures.store"),
            $seizureData,
        );

        $response->assertRedirect(route("seizures.index"));
        $response->assertSessionHasNoErrors();
    }

    public function test_both_duration_fields_can_be_zero(): void
    {
        $user = User::factory()->create();

        $seizureData = [
            "start_time" => now()->subHours(2),
            "end_time" => now()->subHours(1),
            "duration_minutes" => 0,
            "duration_seconds" => 0,
            "severity" => 7,
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("seizures.store"),
            $seizureData,
        );

        $response->assertRedirect(route("seizures.index"));
        $response->assertSessionHasNoErrors();
    }

    public function test_update_validation_follows_same_rules(): void
    {
        $user = User::factory()->create();
        $seizure = \App\Models\Seizure::factory()->create([
            "user_id" => $user->id,
        ]);

        // Test invalid minutes on update
        $updateData = [
            "start_time" => $seizure->start_time,
            "end_time" => $seizure->end_time,
            "duration_minutes" => 1500,
            "duration_seconds" => 30,
            "severity" => 5,
            "on_period" => false,
            "ambulance_called" => false,
            "slept_after" => true,
        ];

        $response = $this->actingAs($user)->putWithCsrf(
            route("seizures.update", $seizure),
            $updateData,
        );

        $response->assertSessionHasErrors("duration_minutes");
        $this->assertStringContainsString(
            "cannot exceed 999",
            $response->getSession()->get("errors")->first("duration_minutes"),
        );
    }

    public function test_duration_validation_with_decimal_values(): void
    {
        $user = User::factory()->create();

        $seizureData = [
            "start_time" => now()->subHours(2),
            "end_time" => now()->subHours(1),
            "duration_minutes" => 5.5,
            "duration_seconds" => 30,
            "severity" => 7,
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("seizures.store"),
            $seizureData,
        );

        $response->assertSessionHasErrors("duration_minutes");
        $this->assertStringContainsString(
            "must be a whole number",
            $response->getSession()->get("errors")->first("duration_minutes"),
        );
    }

    public function test_duration_validation_with_string_numbers(): void
    {
        $user = User::factory()->create();

        $seizureData = [
            "start_time" => now()->subHours(2),
            "end_time" => now()->subHours(1),
            "duration_minutes" => "5",
            "duration_seconds" => "30",
            "severity" => 7,
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("seizures.store"),
            $seizureData,
        );

        $response->assertRedirect(route("seizures.index"));
        $response->assertSessionHasNoErrors();
    }

    public function test_edge_case_maximum_valid_duration(): void
    {
        $user = User::factory()->create();

        $seizureData = [
            "start_time" => now()->subHours(2),
            "end_time" => now()->subHours(1),
            "duration_minutes" => 999,
            "duration_seconds" => 59,
            "severity" => 7,
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("seizures.store"),
            $seizureData,
        );

        $response->assertRedirect(route("seizures.index"));
        $response->assertSessionHasNoErrors();

        // Should store as (999 * 60) + 59 = 59999 seconds
        $this->assertDatabaseHas("seizures", [
            "user_id" => $user->id,
            "duration_seconds" => 59999,
        ]);
    }

    public function test_duration_validation_preserves_other_field_validation(): void
    {
        $user = User::factory()->create();

        $seizureData = [
            "start_time" => now()->subHours(2),
            "end_time" => now()->subHours(1),
            "duration_minutes" => 5,
            "duration_seconds" => 30,
            "severity" => 15, // Invalid severity (max 10)
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("seizures.store"),
            $seizureData,
        );

        $response->assertSessionHasErrors("severity");
        $response->assertSessionDoesntHaveErrors("duration_minutes");
        $response->assertSessionDoesntHaveErrors("duration_seconds");
    }

    public function test_duration_validation_error_messages_are_descriptive(): void
    {
        $user = User::factory()->create();

        // Test minutes error message
        $seizureData = [
            "start_time" => now()->subHours(2),
            "end_time" => now()->subHours(1),
            "duration_minutes" => -1,
            "duration_seconds" => 70,
            "severity" => 7,
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("seizures.store"),
            $seizureData,
        );

        $errors = $response->getSession()->get("errors");

        $this->assertStringContainsString(
            "Duration minutes cannot be negative",
            $errors->first("duration_minutes"),
        );

        $this->assertStringContainsString(
            "Duration seconds cannot exceed 59",
            $errors->first("duration_seconds"),
        );
    }

    public function test_request_prepares_duration_total_correctly(): void
    {
        $user = User::factory()->create();

        $seizureData = [
            "start_time" => now()->subHours(2),
            "end_time" => now()->subHours(1),
            "duration_minutes" => 7,
            "duration_seconds" => 45,
            "severity" => 6,
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("seizures.store"),
            $seizureData,
        );

        $response->assertRedirect(route("seizures.index"));

        // Should store total seconds (7 * 60 + 45 = 465)
        $this->assertDatabaseHas("seizures", [
            "user_id" => $user->id,
            "duration_seconds" => 465,
            "severity" => 6,
        ]);
    }
}
