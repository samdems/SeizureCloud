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

    public function test_seizure_stores_all_comprehensive_fields(): void
    {
        $user = User::factory()->create();

        $seizureData = [
            "start_time" => now()->subHours(2),
            "end_time" => now()->subHours(1),
            "duration_minutes" => 60,
            "severity" => 7,

            // Seizure type
            "seizure_type" => "focal_aware",

            // Video evidence
            "has_video_evidence" => true,
            "video_notes" => "Video recorded by family member",

            // Triggers
            "triggers" => ["stress", "lack_of_sleep"],
            "other_triggers" => "Bright lights in the room",

            // Pre-ictal symptoms
            "pre_ictal_symptoms" => ["aura", "mood_change"],
            "pre_ictal_notes" => "Felt strange aura 10 minutes before",

            // Post-ictal recovery
            "recovery_time" => "moderate",
            "post_ictal_confusion" => true,
            "post_ictal_headache" => false,
            "recovery_notes" => "Took about 1 hour to feel normal",

            // Period and medical info
            "on_period" => true,
            "days_since_period" => 3,

            // Medication adherence
            "medication_adherence" => "good",
            "recent_medication_change" => true,
            "experiencing_side_effects" => false,
            "medication_notes" => "Increased dosage last week",

            // General wellbeing
            "wellbeing_rating" => "fair",
            "sleep_quality" => "poor",
            "wellbeing_notes" => "Been stressed with work",

            // NHS contact and emergency
            "nhs_contact_type" => "111",
            "postictal_state_end" => now()->subMinutes(30),
            "ambulance_called" => false,
            "slept_after" => true,
            "notes" => "Comprehensive test seizure record",
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("seizures.store"),
            $seizureData,
        );

        $response->assertRedirect(route("seizures.index"));
        $response->assertSessionHas("success");

        // Verify all fields are stored correctly
        $seizure = Seizure::where("user_id", $user->id)->first();
        $this->assertNotNull($seizure);

        $this->assertEquals("focal_aware", $seizure->seizure_type);
        $this->assertTrue($seizure->has_video_evidence);
        $this->assertEquals(
            "Video recorded by family member",
            $seizure->video_notes,
        );
        $this->assertEquals(["stress", "lack_of_sleep"], $seizure->triggers);
        $this->assertEquals(
            "Bright lights in the room",
            $seizure->other_triggers,
        );
        $this->assertEquals(
            ["aura", "mood_change"],
            $seizure->pre_ictal_symptoms,
        );
        $this->assertEquals(
            "Felt strange aura 10 minutes before",
            $seizure->pre_ictal_notes,
        );
        $this->assertEquals("moderate", $seizure->recovery_time);
        $this->assertTrue($seizure->post_ictal_confusion);
        $this->assertFalse($seizure->post_ictal_headache);
        $this->assertEquals(
            "Took about 1 hour to feel normal",
            $seizure->recovery_notes,
        );
        $this->assertTrue($seizure->on_period);
        $this->assertEquals(3, $seizure->days_since_period);
        $this->assertEquals("good", $seizure->medication_adherence);
        $this->assertTrue($seizure->recent_medication_change);
        $this->assertFalse($seizure->experiencing_side_effects);
        $this->assertEquals(
            "Increased dosage last week",
            $seizure->medication_notes,
        );
        $this->assertEquals("fair", $seizure->wellbeing_rating);
        $this->assertEquals("poor", $seizure->sleep_quality);
        $this->assertEquals(
            "Been stressed with work",
            $seizure->wellbeing_notes,
        );
        $this->assertEquals("111", $seizure->nhs_contact_type);
        $this->assertNotNull($seizure->postictal_state_end);
        $this->assertFalse($seizure->ambulance_called);
        $this->assertTrue($seizure->slept_after);
        $this->assertEquals(
            "Comprehensive test seizure record",
            $seizure->notes,
        );
    }

    public function test_seizure_updates_all_comprehensive_fields(): void
    {
        $user = User::factory()->create();

        // Create initial seizure with basic data
        $seizure = Seizure::factory()->create([
            "user_id" => $user->id,
            "severity" => 5,
            "seizure_type" => "unknown",
            "has_video_evidence" => false,
        ]);

        // Update with comprehensive data
        $updateData = [
            "start_time" => $seizure->start_time,
            "end_time" => $seizure->start_time->copy()->addMinutes(90),
            "duration_minutes" => 90,
            "severity" => 8,

            // Seizure type
            "seizure_type" => "generalized_tonic_clonic",

            // Video evidence
            "has_video_evidence" => true,
            "video_notes" => "Updated video notes",

            // Triggers
            "triggers" => ["missed_medication", "illness"],
            "other_triggers" => "Updated trigger description",

            // Pre-ictal symptoms
            "pre_ictal_symptoms" => ["headache", "confusion"],
            "pre_ictal_notes" => "Updated pre-ictal notes",

            // Post-ictal recovery
            "recovery_time" => "long",
            "post_ictal_confusion" => false,
            "post_ictal_headache" => true,
            "recovery_notes" => "Updated recovery notes",

            // Period and medical info
            "on_period" => false,
            "days_since_period" => 15,

            // Medication adherence
            "medication_adherence" => "excellent",
            "recent_medication_change" => false,
            "experiencing_side_effects" => true,
            "medication_notes" => "Updated medication notes",

            // General wellbeing
            "wellbeing_rating" => "excellent",
            "sleep_quality" => "excellent",
            "wellbeing_notes" => "Updated wellbeing notes",

            // NHS contact and emergency
            "nhs_contact_type" => "999",
            "postictal_state_end" => now(),
            "ambulance_called" => true,
            "slept_after" => false,
            "notes" => "Updated comprehensive seizure notes",
        ];

        $response = $this->actingAs($user)->putWithCsrf(
            route("seizures.update", $seizure),
            $updateData,
        );

        $response->assertRedirect(route("seizures.index"));
        $response->assertSessionHas("success");

        // Refresh the seizure from database
        $seizure->refresh();

        // Verify all fields are updated correctly
        $this->assertEquals(8, $seizure->severity);
        $this->assertEquals("generalized_tonic_clonic", $seizure->seizure_type);
        $this->assertTrue($seizure->has_video_evidence);
        $this->assertEquals("Updated video notes", $seizure->video_notes);
        $this->assertEquals(
            ["missed_medication", "illness"],
            $seizure->triggers,
        );
        $this->assertEquals(
            "Updated trigger description",
            $seizure->other_triggers,
        );
        $this->assertEquals(
            ["headache", "confusion"],
            $seizure->pre_ictal_symptoms,
        );
        $this->assertEquals(
            "Updated pre-ictal notes",
            $seizure->pre_ictal_notes,
        );
        $this->assertEquals("long", $seizure->recovery_time);
        $this->assertFalse($seizure->post_ictal_confusion);
        $this->assertTrue($seizure->post_ictal_headache);
        $this->assertEquals("Updated recovery notes", $seizure->recovery_notes);
        $this->assertFalse($seizure->on_period);
        $this->assertEquals(15, $seizure->days_since_period);
        $this->assertEquals("excellent", $seizure->medication_adherence);
        $this->assertFalse($seizure->recent_medication_change);
        $this->assertTrue($seizure->experiencing_side_effects);
        $this->assertEquals(
            "Updated medication notes",
            $seizure->medication_notes,
        );
        $this->assertEquals("excellent", $seizure->wellbeing_rating);
        $this->assertEquals("excellent", $seizure->sleep_quality);
        $this->assertEquals(
            "Updated wellbeing notes",
            $seizure->wellbeing_notes,
        );
        $this->assertEquals("999", $seizure->nhs_contact_type);
        $this->assertNotNull($seizure->postictal_state_end);
        $this->assertTrue($seizure->ambulance_called);
        $this->assertFalse($seizure->slept_after);
        $this->assertEquals(
            "Updated comprehensive seizure notes",
            $seizure->notes,
        );
    }

    public function test_seizure_validation_rejects_invalid_trigger_values(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postWithCsrf(
            route("seizures.store"),
            [
                "start_time" => now(),
                "severity" => 5,
                "triggers" => ["invalid_trigger"],
            ],
        );

        $response->assertSessionHasErrors("triggers.0");
    }

    public function test_seizure_validation_rejects_invalid_pre_ictal_symptoms(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postWithCsrf(
            route("seizures.store"),
            [
                "start_time" => now(),
                "severity" => 5,
                "pre_ictal_symptoms" => ["invalid_symptom"],
            ],
        );

        $response->assertSessionHasErrors("pre_ictal_symptoms.0");
    }

    public function test_seizure_validation_rejects_invalid_seizure_type(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postWithCsrf(
            route("seizures.store"),
            [
                "start_time" => now(),
                "severity" => 5,
                "seizure_type" => "invalid_type",
            ],
        );

        $response->assertSessionHasErrors("seizure_type");
    }

    public function test_seizure_validation_rejects_invalid_medication_adherence(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postWithCsrf(
            route("seizures.store"),
            [
                "start_time" => now(),
                "severity" => 5,
                "medication_adherence" => "invalid_level",
            ],
        );

        $response->assertSessionHasErrors("medication_adherence");
    }

    public function test_seizure_validation_rejects_invalid_wellbeing_rating(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postWithCsrf(
            route("seizures.store"),
            [
                "start_time" => now(),
                "severity" => 5,
                "wellbeing_rating" => "invalid_rating",
            ],
        );

        $response->assertSessionHasErrors("wellbeing_rating");
    }

    public function test_seizure_validation_enforces_text_field_length_limits(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postWithCsrf(
            route("seizures.store"),
            [
                "start_time" => now(),
                "severity" => 5,
                "video_notes" => str_repeat("a", 1001), // Over 1000 character limit
                "other_triggers" => str_repeat("b", 501), // Over 500 character limit
            ],
        );

        $response->assertSessionHasErrors(["video_notes", "other_triggers"]);
    }

    public function test_seizure_update_validation_enforces_same_rules(): void
    {
        $user = User::factory()->create();
        $seizure = Seizure::factory()->create(["user_id" => $user->id]);

        $response = $this->actingAs($user)->putWithCsrf(
            route("seizures.update", $seizure),
            [
                "start_time" => $seizure->start_time,
                "severity" => 5,
                "seizure_type" => "invalid_type",
                "triggers" => ["invalid_trigger"],
            ],
        );

        $response->assertSessionHasErrors(["seizure_type", "triggers.0"]);
    }

    public function test_seizure_can_be_saved_with_optional_fields_empty(): void
    {
        $user = User::factory()->create();

        $seizureData = [
            "start_time" => now()->subHours(2),
            "severity" => 6,
            // All other fields are optional and not provided
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("seizures.store"),
            $seizureData,
        );

        $response->assertRedirect(route("seizures.index"));
        $response->assertSessionHas("success");

        $seizure = Seizure::where("user_id", $user->id)->first();
        $this->assertNotNull($seizure);
        $this->assertEquals(6, $seizure->severity);
        $this->assertNull($seizure->seizure_type);
        $this->assertFalse($seizure->has_video_evidence);
        $this->assertNull($seizure->video_notes);
        $this->assertNull($seizure->triggers);
        $this->assertNull($seizure->medication_adherence);
    }
}
