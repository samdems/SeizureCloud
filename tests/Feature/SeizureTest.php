<?php

namespace Tests\Feature;

use App\Models\Medication;
use App\Models\MedicationLog;
use App\Models\MedicationSchedule;
use App\Models\Seizure;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeizureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_seizure_index(): void
    {
        $user = User::factory()->create();
        Seizure::factory()
            ->count(3)
            ->create(["user_id" => $user->id]);

        $response = $this->actingAs($user)->get(route("seizures.index"));

        $response->assertStatus(200);
        $response->assertViewIs("seizures.index");
        $response->assertViewHas("seizures");
    }

    public function test_user_can_create_seizure(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route("seizures.create"));

        $response->assertStatus(200);
        $response->assertViewIs("seizures.create");
    }

    public function test_user_can_store_seizure(): void
    {
        $user = User::factory()->create();

        $seizureData = [
            "start_time" => now()->subHours(2),
            "end_time" => now()->subHours(1),
            "severity" => 7,
            "on_period" => true,
            "nhs_contact_type" => "111",
            "ambulance_called" => false,
            "slept_after" => true,
            "notes" => "Test seizure notes",
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("seizures.store"),
            $seizureData,
        );

        $response->assertRedirect(route("seizures.index"));
        $response->assertSessionHas("success");
        $this->assertDatabaseHas("seizures", [
            "user_id" => $user->id,
            "severity" => 7,
            "notes" => "Test seizure notes",
        ]);
    }

    public function test_user_can_view_single_seizure(): void
    {
        $user = User::factory()->create();
        $seizure = Seizure::factory()->create(["user_id" => $user->id]);

        $response = $this->actingAs($user)->get(
            route("seizures.show", $seizure),
        );

        $response->assertStatus(200);
        $response->assertViewIs("seizures.show");
        $response->assertViewHas("seizure");
        $response->assertViewHas("medications");
    }

    public function test_user_cannot_view_other_users_seizure(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $seizure = Seizure::factory()->create(["user_id" => $otherUser->id]);

        $response = $this->actingAs($user)->get(
            route("seizures.show", $seizure),
        );

        $response->assertStatus(403);
    }

    public function test_user_can_edit_seizure(): void
    {
        $user = User::factory()->create();
        $seizure = Seizure::factory()->create(["user_id" => $user->id]);

        $response = $this->actingAs($user)->get(
            route("seizures.edit", $seizure),
        );

        $response->assertStatus(200);
        $response->assertViewIs("seizures.edit");
        $response->assertViewHas("seizure");
    }

    public function test_user_can_update_seizure(): void
    {
        $user = User::factory()->create();
        $seizure = Seizure::factory()->create([
            "user_id" => $user->id,
            "severity" => 5,
        ]);

        $updateData = [
            "start_time" => $seizure->start_time,
            "end_time" => $seizure->end_time,
            "severity" => 8,
            "on_period" => false,
            "ambulance_called" => false,
            "slept_after" => true,
            "notes" => "Updated notes",
        ];

        $response = $this->actingAs($user)->putWithCsrf(
            route("seizures.update", $seizure),
            $updateData,
        );

        $response->assertRedirect(route("seizures.index"));
        $response->assertSessionHas("success");
        $this->assertDatabaseHas("seizures", [
            "id" => $seizure->id,
            "severity" => 8,
            "notes" => "Updated notes",
        ]);
    }

    public function test_user_can_delete_seizure(): void
    {
        $user = User::factory()->create();
        $seizure = Seizure::factory()->create(["user_id" => $user->id]);

        $response = $this->actingAs($user)->deleteWithCsrf(
            route("seizures.destroy", $seizure),
        );

        $response->assertRedirect(route("seizures.index"));
        $response->assertSessionHas("success");
        $this->assertDatabaseMissing("seizures", ["id" => $seizure->id]);
    }

    public function test_seizure_validation_requires_start_time(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postWithCsrf(
            route("seizures.store"),
            [
                "severity" => 5,
            ],
        );

        $response->assertSessionHasErrors("start_time");
    }

    public function test_seizure_validation_requires_severity(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postWithCsrf(
            route("seizures.store"),
            [
                "start_time" => now(),
            ],
        );

        $response->assertSessionHasErrors("severity");
    }

    public function test_seizure_validation_severity_between_1_and_10(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postWithCsrf(
            route("seizures.store"),
            [
                "start_time" => now(),
                "severity" => 11,
            ],
        );

        $response->assertSessionHasErrors("severity");
    }

    public function test_seizure_shows_medication_adherence_correctly(): void
    {
        $user = User::factory()->create();

        // Create a medication with a schedule
        $medication = Medication::factory()->create([
            "user_id" => $user->id,
            "active" => true,
            "name" => "Test Med",
        ]);

        $schedule = MedicationSchedule::factory()->create([
            "medication_id" => $medication->id,
            "scheduled_time" => "08:00:00",
        ]);

        // Create a seizure at 10 AM
        $seizure = Seizure::factory()->create([
            "user_id" => $user->id,
            "start_time" => now()->setTime(10, 0, 0),
        ]);

        // Log that the medication was taken at 8 AM
        MedicationLog::create([
            "medication_id" => $medication->id,
            "medication_schedule_id" => $schedule->id,
            "taken_at" => $seizure->start_time->copy()->setTime(8, 0, 0),
            "dosage_taken" => "500 mg",
            "skipped" => false,
        ]);

        $response = $this->actingAs($user)->get(
            route("seizures.show", $seizure),
        );

        $response->assertStatus(200);
        $response->assertSee("Test Med");
        $response->assertSee("All Taken");
    }

    public function test_seizure_shows_missed_medication_correctly(): void
    {
        $user = User::factory()->create();

        // Create a medication with a schedule
        $medication = Medication::factory()->create([
            "user_id" => $user->id,
            "active" => true,
            "name" => "Test Med",
        ]);

        $schedule = MedicationSchedule::factory()->create([
            "medication_id" => $medication->id,
            "scheduled_time" => "08:00:00",
        ]);

        // Create a seizure at 10 AM
        $seizure = Seizure::factory()->create([
            "user_id" => $user->id,
            "start_time" => now()->setTime(10, 0, 0),
        ]);

        // Log that the medication was skipped
        MedicationLog::create([
            "medication_id" => $medication->id,
            "medication_schedule_id" => $schedule->id,
            "taken_at" => $seizure->start_time->copy()->setTime(8, 0, 0),
            "skipped" => true,
            "skip_reason" => "Forgot",
        ]);

        $response = $this->actingAs($user)->get(
            route("seizures.show", $seizure),
        );

        $response->assertStatus(200);
        $response->assertSee("Test Med");
        $response->assertSee("Missed Doses");
        $response->assertSee("Forgot");
    }

    public function test_seizure_shows_unscheduled_medications_as_grayed_out(): void
    {
        $user = User::factory()->create();

        // Create a medication with a schedule AFTER the seizure
        $medication = Medication::factory()->create([
            "user_id" => $user->id,
            "active" => true,
            "name" => "Test Med",
        ]);

        $schedule = MedicationSchedule::factory()->create([
            "medication_id" => $medication->id,
            "scheduled_time" => "14:00:00", // 2 PM
        ]);

        // Create a seizure at 10 AM (before the scheduled dose)
        $seizure = Seizure::factory()->create([
            "user_id" => $user->id,
            "start_time" => now()->setTime(10, 0, 0),
        ]);

        $response = $this->actingAs($user)->get(
            route("seizures.show", $seizure),
        );

        $response->assertStatus(200);
        $response->assertSee("Test Med");
        $response->assertSee("Not Scheduled");
    }
}
