<?php

namespace Tests\Feature;

use App\Models\Medication;
use App\Models\MedicationLog;
use App\Models\MedicationSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_medications_index(): void
    {
        $user = User::factory()->create();
        Medication::factory()
            ->count(3)
            ->create(["user_id" => $user->id]);

        $response = $this->actingAs($user)->get(route("medications.index"));

        $response->assertStatus(200);
        $response->assertViewIs("medications.index");
        $response->assertViewHas("medications");
    }

    public function test_user_can_create_medication(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route("medications.create"));

        $response->assertStatus(200);
        $response->assertViewIs("medications.create");
    }

    public function test_user_can_store_medication(): void
    {
        $user = User::factory()->create();

        $medicationData = [
            "name" => "Keppra",
            "dosage" => "500",
            "unit" => "mg",
            "description" => "Anti-seizure medication",
            "prescriber" => "Dr. Smith",
            "start_date" => now()->subMonths(3),
            "active" => true,
            "as_needed" => false,
            "notes" => "Take with food",
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("medications.store"),
            $medicationData,
        );

        $response->assertRedirect(route("medications.index"));
        $response->assertSessionHas("success");
        $this->assertDatabaseHas("medications", [
            "user_id" => $user->id,
            "name" => "Keppra",
            "dosage" => "500",
            "unit" => "mg",
        ]);
    }

    public function test_user_can_view_single_medication(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create(["user_id" => $user->id]);

        $response = $this->actingAs($user)->get(
            route("medications.show", $medication),
        );

        $response->assertStatus(200);
        $response->assertViewIs("medications.show");
        $response->assertViewHas("medication");
    }

    public function test_user_cannot_view_other_users_medication(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $medication = Medication::factory()->create([
            "user_id" => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->get(
            route("medications.show", $medication),
        );

        $response->assertStatus(403);
    }

    public function test_user_can_update_medication(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create([
            "user_id" => $user->id,
            "name" => "Original Name",
        ]);

        $updateData = [
            "name" => "Updated Name",
            "dosage" => $medication->dosage,
            "unit" => $medication->unit,
            "active" => $medication->active,
            "as_needed" => $medication->as_needed,
        ];

        $response = $this->actingAs($user)->putWithCsrf(
            route("medications.update", $medication),
            $updateData,
        );

        $response->assertRedirect(route("medications.index"));
        $this->assertDatabaseHas("medications", [
            "id" => $medication->id,
            "name" => "Updated Name",
        ]);
    }

    public function test_user_can_delete_medication(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create(["user_id" => $user->id]);

        $response = $this->actingAs($user)->deleteWithCsrf(
            route("medications.destroy", $medication),
        );

        $response->assertRedirect(route("medications.index"));
        $this->assertDatabaseMissing("medications", ["id" => $medication->id]);
    }

    public function test_medication_validation_requires_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postWithCsrf(
            route("medications.store"),
            [
                "dosage" => "500",
                "unit" => "mg",
            ],
        );

        $response->assertSessionHasErrors("name");
    }

    public function test_medication_dosage_and_unit_are_optional(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postWithCsrf(
            route("medications.store"),
            [
                "name" => "Keppra",
                "active" => true,
                "as_needed" => false,
            ],
        );

        $response->assertRedirect(route("medications.index"));
        $this->assertDatabaseHas("medications", [
            "user_id" => $user->id,
            "name" => "Keppra",
        ]);
    }

    public function test_user_can_add_medication_schedule(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create(["user_id" => $user->id]);

        $scheduleData = [
            "scheduled_time" => "08:00",
            "dosage_multiplier" => 1.0,
            "frequency" => "daily",
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("medications.schedules.store", $medication),
            $scheduleData,
        );

        $response->assertRedirect();
        $this->assertDatabaseHas("medication_schedules", [
            "medication_id" => $medication->id,
        ]);
    }

    public function test_user_can_delete_medication_schedule(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create(["user_id" => $user->id]);
        $schedule = MedicationSchedule::factory()->create([
            "medication_id" => $medication->id,
        ]);

        $response = $this->actingAs($user)->deleteWithCsrf(
            route("medications.schedules.destroy", [$medication, $schedule]),
        );

        $response->assertRedirect();
        $this->assertDatabaseMissing("medication_schedules", [
            "id" => $schedule->id,
        ]);
    }

    public function test_user_can_log_medication_taken(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create(["user_id" => $user->id]);
        $schedule = MedicationSchedule::factory()->create([
            "medication_id" => $medication->id,
        ]);

        $logData = [
            "medication_id" => $medication->id,
            "medication_schedule_id" => $schedule->id,
            "taken_at" => now()->format("Y-m-d H:i"),
            "dosage_taken" => "500 mg",
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("medications.log-taken"),
            $logData,
        );

        $response->assertRedirect();
        $this->assertDatabaseHas("medication_logs", [
            "medication_id" => $medication->id,
            "skipped" => false,
        ]);
    }

    public function test_user_can_log_skipped_medication_with_reason(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create(["user_id" => $user->id]);
        $schedule = MedicationSchedule::factory()->create([
            "medication_id" => $medication->id,
        ]);

        $logData = [
            "medication_id" => $medication->id,
            "medication_schedule_id" => $schedule->id,
            "taken_at" => now()->format("Y-m-d H:i"),
            "skip_reason" => "Forgot",
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("medications.log-skipped"),
            $logData,
        );

        $response->assertRedirect();
        $this->assertDatabaseHas("medication_logs", [
            "medication_id" => $medication->id,
            "skipped" => true,
            "skip_reason" => "Forgot",
        ]);
    }

    public function test_user_can_view_schedule_history(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create(["user_id" => $user->id]);
        $schedule = MedicationSchedule::factory()->create([
            "medication_id" => $medication->id,
        ]);

        MedicationLog::factory()
            ->count(5)
            ->create([
                "medication_id" => $medication->id,
                "medication_schedule_id" => $schedule->id,
            ]);

        $response = $this->actingAs($user)->get(
            route("medications.schedule.history"),
        );

        $response->assertStatus(200);
        $response->assertViewIs("medications.schedule-history");
    }

    public function test_schedule_history_shows_specific_day(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create(["user_id" => $user->id]);
        $schedule = MedicationSchedule::factory()->create([
            "medication_id" => $medication->id,
            "scheduled_time" => "08:00:00",
        ]);

        $targetDate = now()->subDays(3);

        MedicationLog::create([
            "medication_id" => $medication->id,
            "medication_schedule_id" => $schedule->id,
            "taken_at" => $targetDate->copy()->setTime(8, 0, 0),
            "dosage_taken" => "500 mg",
            "skipped" => false,
        ]);

        $response = $this->actingAs($user)->get(
            route("medications.schedule.history", [
                "date" => $targetDate->format("Y-m-d"),
            ]),
        );

        $response->assertStatus(200);
        $response->assertViewIs("medications.schedule-history");
    }

    public function test_medication_schedule_calculates_dosage_correctly(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create([
            "user_id" => $user->id,
            "dosage" => "500",
            "unit" => "mg",
        ]);

        $schedule = MedicationSchedule::factory()->create([
            "medication_id" => $medication->id,
            "dosage_multiplier" => 2.0,
        ]);

        $this->assertEquals(
            "1000.00 mg",
            $schedule->getCalculatedDosageWithUnit(),
        );
    }

    public function test_inactive_medications_are_filtered_correctly(): void
    {
        $user = User::factory()->create();

        Medication::factory()->create([
            "user_id" => $user->id,
            "active" => true,
            "name" => "Active Med",
        ]);

        Medication::factory()->create([
            "user_id" => $user->id,
            "active" => false,
            "name" => "Inactive Med",
        ]);

        $response = $this->actingAs($user)->get(route("medications.index"));

        $response->assertStatus(200);
        $response->assertSee("Active Med");
    }

    public function test_user_can_update_medication_log(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create(["user_id" => $user->id]);
        $schedule = MedicationSchedule::factory()->create([
            "medication_id" => $medication->id,
        ]);
        $log = MedicationLog::factory()->create([
            "medication_id" => $medication->id,
            "medication_schedule_id" => $schedule->id,
            "dosage_taken" => "500 mg",
            "notes" => "Original notes",
        ]);

        $updateData = [
            "taken_at" => $log->taken_at->format("Y-m-d H:i"),
            "dosage_taken" => "750 mg",
            "notes" => "Updated notes",
        ];

        $response = $this->actingAs($user)->putWithCsrf(
            route("medications.log-update", $log),
            $updateData,
        );

        $response->assertRedirect();
        $response->assertSessionHas("success");
        $this->assertDatabaseHas("medication_logs", [
            "id" => $log->id,
            "dosage_taken" => "750 mg",
            "notes" => "Updated notes",
        ]);
    }

    public function test_user_can_delete_medication_log(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create(["user_id" => $user->id]);
        $schedule = MedicationSchedule::factory()->create([
            "medication_id" => $medication->id,
        ]);
        $log = MedicationLog::factory()->create([
            "medication_id" => $medication->id,
            "medication_schedule_id" => $schedule->id,
        ]);

        $response = $this->actingAs($user)->deleteWithCsrf(
            route("medications.log-destroy", $log),
        );

        $response->assertRedirect();
        $response->assertSessionHas("success");
        $this->assertDatabaseMissing("medication_logs", ["id" => $log->id]);
    }

    public function test_user_cannot_update_other_users_medication_log(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $medication = Medication::factory()->create([
            "user_id" => $otherUser->id,
        ]);
        $schedule = MedicationSchedule::factory()->create([
            "medication_id" => $medication->id,
        ]);
        $log = MedicationLog::factory()->create([
            "medication_id" => $medication->id,
            "medication_schedule_id" => $schedule->id,
        ]);

        $updateData = [
            "taken_at" => $log->taken_at->format("Y-m-d H:i"),
            "dosage_taken" => "750 mg",
        ];

        $response = $this->actingAs($user)->putWithCsrf(
            route("medications.log-update", $log),
            $updateData,
        );

        $response->assertStatus(403);
    }

    public function test_user_cannot_delete_other_users_medication_log(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $medication = Medication::factory()->create([
            "user_id" => $otherUser->id,
        ]);
        $schedule = MedicationSchedule::factory()->create([
            "medication_id" => $medication->id,
        ]);
        $log = MedicationLog::factory()->create([
            "medication_id" => $medication->id,
            "medication_schedule_id" => $schedule->id,
        ]);

        $response = $this->actingAs($user)->deleteWithCsrf(
            route("medications.log-destroy", $log),
        );

        $response->assertStatus(403);
    }

    public function test_medication_log_update_validation_requires_taken_at(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create(["user_id" => $user->id]);
        $schedule = MedicationSchedule::factory()->create([
            "medication_id" => $medication->id,
        ]);
        $log = MedicationLog::factory()->create([
            "medication_id" => $medication->id,
            "medication_schedule_id" => $schedule->id,
        ]);

        $updateData = [
            "dosage_taken" => "500 mg",
            // Missing taken_at
        ];

        $response = $this->actingAs($user)->putWithCsrf(
            route("medications.log-update", $log),
            $updateData,
        );

        $response->assertSessionHasErrors("taken_at");
    }

    public function test_medication_log_update_allows_empty_dosage_and_notes(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create(["user_id" => $user->id]);
        $schedule = MedicationSchedule::factory()->create([
            "medication_id" => $medication->id,
        ]);
        $log = MedicationLog::factory()->create([
            "medication_id" => $medication->id,
            "medication_schedule_id" => $schedule->id,
            "dosage_taken" => "500 mg",
            "notes" => "Some notes",
        ]);

        $updateData = [
            "taken_at" => $log->taken_at->format("Y-m-d H:i"),
            "dosage_taken" => "",
            "notes" => "",
        ];

        $response = $this->actingAs($user)->putWithCsrf(
            route("medications.log-update", $log),
            $updateData,
        );

        $response->assertRedirect();
        $this->assertDatabaseHas("medication_logs", [
            "id" => $log->id,
            "dosage_taken" => null,
            "notes" => null,
        ]);
    }
}
