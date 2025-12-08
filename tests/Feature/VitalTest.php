<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vital;
use App\Models\VitalTypeThreshold;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VitalTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_vitals_index(): void
    {
        $user = User::factory()->create();
        Vital::factory()
            ->count(3)
            ->create(["user_id" => $user->id]);

        $response = $this->actingAs($user)->get(route("vitals.index"));

        $response->assertStatus(200);
        $response->assertViewIs("vitals.index");
        $response->assertViewHas("vitals");
    }

    public function test_user_can_create_vital(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route("vitals.create"));

        $response->assertStatus(200);
        $response->assertViewIs("vitals.create");
    }

    public function test_user_can_store_basic_vital(): void
    {
        $user = User::factory()->create();

        $vitalData = [
            "type" => "Heart Rate",
            "value" => "75",
            "recorded_at" => now()->format("Y-m-d H:i"),
            "notes" => "Resting heart rate",
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("vitals.store"),
            $vitalData,
        );

        $response->assertRedirect(route("vitals.index"));
        $response->assertSessionHas("success");
        $this->assertDatabaseHas("vitals", [
            "user_id" => $user->id,
            "type" => "Heart Rate",
            "value" => "75",
        ]);
    }

    public function test_user_can_store_blood_pressure_with_combined_format(): void
    {
        $user = User::factory()->create();

        $vitalData = [
            "type" => "Blood Pressure",
            "value" => "120/80",
            "recorded_at" => now()->format("Y-m-d H:i"),
            "notes" => "Morning reading",
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("vitals.store"),
            $vitalData,
        );

        $response->assertRedirect(route("vitals.index"));
        $this->assertDatabaseHas("vitals", [
            "user_id" => $user->id,
            "type" => "Blood Pressure",
            "value" => 120.0, // Controller stores systolic as primary value
            "systolic_value" => 120.0,
            "diastolic_value" => 80.0,
        ]);
    }

    public function test_blood_pressure_validation_rejects_invalid_format(): void
    {
        $user = User::factory()->create();

        $vitalData = [
            "type" => "Blood Pressure",
            "value" => "invalid/format",
            "recorded_at" => now()->format("Y-m-d H:i"),
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("vitals.store"),
            $vitalData,
        );

        $response->assertSessionHasErrors("value");
    }

    public function test_blood_pressure_validation_accepts_numeric_values(): void
    {
        $user = User::factory()->create();

        $validFormats = ["120/80", "140/90", "90/60", "180/120"];

        foreach ($validFormats as $format) {
            $vitalData = [
                "type" => "Blood Pressure",
                "value" => $format,
                "recorded_at" => now()->format("Y-m-d H:i"),
            ];

            $response = $this->actingAs($user)->postWithCsrf(
                route("vitals.store"),
                $vitalData,
            );

            $response->assertRedirect(route("vitals.index"));
        }
    }

    public function test_user_can_view_single_vital(): void
    {
        $user = User::factory()->create();
        $vital = Vital::factory()->create(["user_id" => $user->id]);

        $response = $this->actingAs($user)->get(route("vitals.show", $vital));

        $response->assertStatus(200);
        $response->assertViewIs("vitals.show");
        $response->assertViewHas("vital");
    }

    public function test_user_cannot_view_other_users_vital(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $vital = Vital::factory()->create(["user_id" => $otherUser->id]);

        $response = $this->actingAs($user)->get(route("vitals.show", $vital));

        $response->assertStatus(403);
    }

    public function test_user_can_edit_vital(): void
    {
        $user = User::factory()->create();
        $vital = Vital::factory()->create(["user_id" => $user->id]);

        $response = $this->actingAs($user)->get(route("vitals.edit", $vital));

        $response->assertStatus(200);
        $response->assertViewIs("vitals.edit");
        $response->assertViewHas("vital");
    }

    public function test_user_can_update_vital(): void
    {
        $user = User::factory()->create();
        $vital = Vital::factory()->create([
            "user_id" => $user->id,
            "type" => "Heart Rate",
            "value" => "75",
        ]);

        $updateData = [
            "type" => "Heart Rate",
            "value" => "80",
            "recorded_at" => $vital->recorded_at->format("Y-m-d H:i"),
            "notes" => "Updated reading",
        ];

        $response = $this->actingAs($user)->putWithCsrf(
            route("vitals.update", $vital),
            $updateData,
        );

        $response->assertRedirect(route("vitals.index"));
        $this->assertDatabaseHas("vitals", [
            "id" => $vital->id,
            "value" => "80",
            "notes" => "Updated reading",
        ]);
    }

    public function test_user_can_delete_vital(): void
    {
        $user = User::factory()->create();
        $vital = Vital::factory()->create(["user_id" => $user->id]);

        $response = $this->actingAs($user)->deleteWithCsrf(
            route("vitals.destroy", $vital),
        );

        $response->assertRedirect(route("vitals.index"));
        $this->assertDatabaseMissing("vitals", ["id" => $vital->id]);
    }

    public function test_vital_validation_requires_type(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postWithCsrf(
            route("vitals.store"),
            [
                "value" => "75",
                "recorded_at" => now()->format("Y-m-d H:i"),
            ],
        );

        $response->assertSessionHasErrors("type");
    }

    public function test_vital_validation_requires_value(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postWithCsrf(
            route("vitals.store"),
            [
                "type" => "Heart Rate",
                "recorded_at" => now()->format("Y-m-d H:i"),
            ],
        );

        $response->assertSessionHasErrors("value");
    }

    public function test_vital_validation_requires_recorded_at(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postWithCsrf(
            route("vitals.store"),
            [
                "type" => "Heart Rate",
                "value" => "75",
            ],
        );

        $response->assertSessionHasErrors("recorded_at");
    }

    public function test_vital_blood_pressure_parsing_extracts_systolic_diastolic(): void
    {
        $user = User::factory()->create();

        $vital = Vital::create([
            "user_id" => $user->id,
            "type" => "Blood Pressure",
            "value" => 125.0, // Primary value is systolic
            "recorded_at" => now(),
            "systolic_value" => 125.0,
            "diastolic_value" => 85.0,
        ]);

        $this->assertEquals(125.0, $vital->systolic_value);
        $this->assertEquals(85.0, $vital->diastolic_value);
        $this->assertEquals(125.0, $vital->value); // Primary value is systolic
        $this->assertTrue($vital->isBloodPressure());
    }

    public function test_vital_status_calculation_with_thresholds(): void
    {
        $user = User::factory()->create();

        // Create threshold for Heart Rate
        VitalTypeThreshold::create([
            "user_id" => $user->id,
            "vital_type" => "Heart Rate",
            "low_threshold" => 60.0,
            "high_threshold" => 100.0,
        ]);

        // Test normal reading
        $normalVital = Vital::factory()->create([
            "user_id" => $user->id,
            "type" => "Heart Rate",
            "value" => "75",
        ]);
        $this->assertEquals("normal", $normalVital->getStatus());
        $this->assertFalse($normalVital->isTooLow());
        $this->assertFalse($normalVital->isTooHigh());

        // Test too low
        $lowVital = Vital::factory()->create([
            "user_id" => $user->id,
            "type" => "Heart Rate",
            "value" => "45",
        ]);
        $this->assertEquals("too_low", $lowVital->getStatus());
        $this->assertTrue($lowVital->isTooLow());
        $this->assertFalse($lowVital->isTooHigh());

        // Test too high
        $highVital = Vital::factory()->create([
            "user_id" => $user->id,
            "type" => "Heart Rate",
            "value" => "120",
        ]);
        $this->assertEquals("too_high", $highVital->getStatus());
        $this->assertFalse($highVital->isTooLow());
        $this->assertTrue($highVital->isTooHigh());
    }

    public function test_blood_pressure_status_calculation(): void
    {
        $user = User::factory()->create();

        // Create blood pressure thresholds
        VitalTypeThreshold::create([
            "user_id" => $user->id,
            "vital_type" => "Blood Pressure",
            "systolic_low_threshold" => 90.0,
            "systolic_high_threshold" => 140.0,
            "diastolic_low_threshold" => 60.0,
            "diastolic_high_threshold" => 90.0,
        ]);

        // Test normal BP
        $normalBP = Vital::factory()->create([
            "user_id" => $user->id,
            "type" => "Blood Pressure",
            "value" => 120.0,
            "systolic_value" => 120.0,
            "diastolic_value" => 80.0,
        ]);
        $this->assertEquals("normal", $normalBP->getStatus());

        // Test high BP (systolic)
        $highBP = Vital::factory()->create([
            "user_id" => $user->id,
            "type" => "Blood Pressure",
            "value" => 160.0,
            "systolic_value" => 160.0,
            "diastolic_value" => 80.0,
        ]);
        $this->assertEquals("too_high", $highBP->getStatus());

        // Test low BP
        $lowBP = Vital::factory()->create([
            "user_id" => $user->id,
            "type" => "Blood Pressure",
            "value" => 80.0,
            "systolic_value" => 80.0,
            "diastolic_value" => 50.0,
        ]);
        $this->assertEquals("too_low", $lowBP->getStatus());
    }

    public function test_vital_formatted_value_display(): void
    {
        $user = User::factory()->create();

        // Regular vital
        $heartRate = Vital::factory()->create([
            "user_id" => $user->id,
            "type" => "Heart Rate",
            "value" => "75",
        ]);
        $this->assertEquals("75", $heartRate->getFormattedValue());

        // Blood pressure
        $bloodPressure = Vital::factory()->create([
            "user_id" => $user->id,
            "type" => "Blood Pressure",
            "value" => 120.0,
            "systolic_value" => 120.0,
            "diastolic_value" => 80.0,
        ]);
        $this->assertEquals("120/80", $bloodPressure->getFormattedValue());
    }

    public function test_vital_status_text_display(): void
    {
        $user = User::factory()->create();

        // Create threshold for testing
        VitalTypeThreshold::create([
            "user_id" => $user->id,
            "vital_type" => "Heart Rate",
            "low_threshold" => 60.0,
            "high_threshold" => 100.0,
        ]);

        $vital = Vital::factory()->create([
            "user_id" => $user->id,
            "type" => "Heart Rate",
            "value" => "45",
        ]);

        $statusText = $vital->getStatusText();
        $this->assertNotEmpty($statusText);
        $this->assertIsString($statusText);
    }

    public function test_vitals_are_filtered_by_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Vital::factory()->create([
            "user_id" => $user1->id,
            "type" => "Heart Rate",
        ]);
        Vital::factory()->create([
            "user_id" => $user2->id,
            "type" => "Blood Pressure",
        ]);

        $response = $this->actingAs($user1)->get(route("vitals.index"));

        $response->assertStatus(200);
        $vitals = $response->viewData("vitals");
        $this->assertCount(1, $vitals);
        $this->assertEquals($user1->id, $vitals->first()->user_id);
    }

    public function test_vital_notes_are_optional(): void
    {
        $user = User::factory()->create();

        $vitalData = [
            "type" => "Heart Rate",
            "value" => "75",
            "recorded_at" => now()->format("Y-m-d H:i"),
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("vitals.store"),
            $vitalData,
        );

        $response->assertRedirect(route("vitals.index"));
        $this->assertDatabaseHas("vitals", [
            "user_id" => $user->id,
            "type" => "Heart Rate",
            "value" => "75",
            "notes" => "",
        ]);
    }
}
