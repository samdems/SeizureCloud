<?php

namespace Tests\Feature;

use App\Models\Medication;
use App\Models\MedicationLog;
use App\Models\MedicationSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicationTimingTest extends TestCase
{
    use RefreshDatabase;

    public function test_intended_time_is_automatically_set_for_scheduled_medications(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create(["user_id" => $user->id]);
        $schedule = MedicationSchedule::factory()->create([
            "medication_id" => $medication->id,
            "scheduled_time" => "08:00:00",
        ]);

        $takenTime = now()->setTime(8, 15, 0);

        $logData = [
            "medication_id" => $medication->id,
            "medication_schedule_id" => $schedule->id,
            "taken_at" => $takenTime->format("Y-m-d H:i"),
            "dosage_taken" => "500 mg",
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("medications.log-taken"),
            $logData,
        );

        $response->assertRedirect();

        $log = MedicationLog::where("medication_id", $medication->id)->first();
        $this->assertNotNull($log->intended_time);
        $this->assertEquals("08:00", $log->intended_time->format("H:i"));
    }

    public function test_user_can_manually_set_intended_time(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create([
            "user_id" => $user->id,
            "as_needed" => true,
        ]);

        $takenTime = now()->setTime(14, 30, 0);
        $intendedTime = now()->setTime(14, 0, 0); // Symptom occurred 30 minutes earlier

        $logData = [
            "medication_id" => $medication->id,
            "taken_at" => $takenTime->format("Y-m-d H:i"),
            "intended_time" => $intendedTime->format("Y-m-d H:i"),
            "dosage_taken" => "500 mg",
            "notes" => "Took for headache that started earlier",
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("medications.log-taken"),
            $logData,
        );

        $response->assertRedirect();
        $this->assertDatabaseHas("medication_logs", [
            "medication_id" => $medication->id,
            "intended_time" => $intendedTime->format("Y-m-d H:i:s"),
        ]);
    }

    public function test_medication_log_calculates_timing_difference_correctly(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create(["user_id" => $user->id]);

        // Test on-time medication (within 5 minute tolerance)
        $onTimeLog = MedicationLog::create([
            "medication_id" => $medication->id,
            "taken_at" => now()->setTime(8, 3, 0),
            "intended_time" => now()->setTime(8, 0, 0),
            "skipped" => false,
        ]);

        $this->assertFalse($onTimeLog->isTakenAtDifferentTime());
        $this->assertEquals("On time", $onTimeLog->getTimeDifference());

        // Test late medication
        $lateLog = MedicationLog::create([
            "medication_id" => $medication->id,
            "taken_at" => now()->setTime(8, 30, 0),
            "intended_time" => now()->setTime(8, 0, 0),
            "skipped" => false,
        ]);

        $this->assertTrue($lateLog->isTakenAtDifferentTime());
        $this->assertEquals("+30 minutes late", $lateLog->getTimeDifference());

        // Test early medication
        $earlyLog = MedicationLog::create([
            "medication_id" => $medication->id,
            "taken_at" => now()->setTime(7, 45, 0),
            "intended_time" => now()->setTime(8, 0, 0),
            "skipped" => false,
        ]);

        $this->assertTrue($earlyLog->isTakenAtDifferentTime());
        $this->assertEquals("15 minutes early", $earlyLog->getTimeDifference());
    }

    public function test_medication_log_timing_description_includes_context(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create(["user_id" => $user->id]);

        // Test skipped medication
        $skippedLog = MedicationLog::create([
            "medication_id" => $medication->id,
            "taken_at" => now(),
            "intended_time" => now(),
            "skipped" => true,
        ]);

        $this->assertEquals("Skipped", $skippedLog->getTakenTimeDescription());

        // Test on-time medication
        $onTimeLog = MedicationLog::create([
            "medication_id" => $medication->id,
            "taken_at" => now()->setTime(8, 2, 0),
            "intended_time" => now()->setTime(8, 0, 0),
            "skipped" => false,
        ]);

        $description = $onTimeLog->getTakenTimeDescription();
        $this->assertStringContains("Taken on time at", $description);
        $this->assertStringContains("8:02 AM", $description);

        // Test late medication
        $lateLog = MedicationLog::create([
            "medication_id" => $medication->id,
            "taken_at" => now()->setTime(8, 30, 0),
            "intended_time" => now()->setTime(8, 0, 0),
            "skipped" => false,
        ]);

        $description = $lateLog->getTakenTimeDescription();
        $this->assertStringContains("Taken at", $description);
        $this->assertStringContains("8:30 AM", $description);
        $this->assertStringContains("+30 minutes late", $description);
    }

    public function test_bulk_taken_sets_appropriate_intended_times(): void
    {
        $user = User::factory()->create();

        $morningMed = Medication::factory()->create(["user_id" => $user->id]);
        $afternoonMed = Medication::factory()->create(["user_id" => $user->id]);

        $morningSchedule = MedicationSchedule::factory()->create([
            "medication_id" => $morningMed->id,
            "scheduled_time" => "08:00:00",
        ]);
        $afternoonSchedule = MedicationSchedule::factory()->create([
            "medication_id" => $afternoonMed->id,
            "scheduled_time" => "08:30:00", // Still in morning period
        ]);

        $takenTime = now()->setTime(9, 0, 0); // Taken at 9 AM

        $bulkData = [
            "period" => "morning",
            "taken_at" => $takenTime->format("Y-m-d H:i"),
            "notes" => "Took all morning medications together",
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("medications.log-bulk-taken"),
            $bulkData,
        );

        $response->assertRedirect();

        // Check that both medications have logs with correct intended times
        $morningLog = MedicationLog::where("medication_id", $morningMed->id)->first();
        $afternoonLog = MedicationLog::where("medication_id", $afternoonMed->id)->first();

        $this->assertNotNull($morningLog);
        $this->assertNotNull($afternoonLog);
        $this->assertEquals("08:00", $morningLog->intended_time->format("H:i"));
        $this->assertEquals("08:30", $afternoonLog->intended_time->format("H:i"));

        // Both should be marked as taken at 9:00 AM
        $this->assertEquals("09:00", $morningLog->taken_at->format("H:i"));
        $this->assertEquals("09:00", $afternoonLog->taken_at->format("H:i"));

        // Both should be late
        $this->assertTrue($morningLog->isTakenAtDifferentTime());
        $this->assertTrue($afternoonLog->isTakenAtDifferentTime());
    }

    public function test_skipped_medication_preserves_intended_time(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create(["user_id" => $user->id]);
        $schedule = MedicationSchedule::factory()->create([
            "medication_id" => $medication->id,
            "scheduled_time" => "08:00:00",
        ]);

        $intendedTime = now()->setTime(8, 0, 0);

        $logData = [
            "medication_id" => $medication->id,
            "medication_schedule_id" => $schedule->id,
            "intended_time" => $intendedTime->format("Y-m-d H:i"),
            "skip_reason" => "Forgot",
            "notes" => "Overslept and missed morning dose",
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
            "intended_time" => $intendedTime->format("Y-m-d H:i:s"),
        ]);
    }

    public function test_medication_log_update_can_modify_intended_time(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create(["user_id" => $user->id]);
        $log = MedicationLog::create([
            "medication_id" => $medication->id,
            "taken_at" => now()->setTime(8, 30, 0),
            "intended_time" => now()->setTime(8, 0, 0),
            "dosage_taken" => "500 mg",
            "skipped" => false,
        ]);

        $newIntendedTime = now()->setTime(8, 15, 0);
        $updateData = [
            "taken_at" => $log->taken_at->format("Y-m-d H:i"),
            "intended_time" => $newIntendedTime->format("Y-m-d H:i"),
            "dosage_taken" => "750 mg",
            "notes" => "Corrected intended time",
        ];

        $response = $this->actingAs($user)->putWithCsrf(
            route("medications.log-update", $log),
            $updateData,
        );

        $response->assertRedirect();

        $log->refresh();
        $this->assertEquals($newIntendedTime->format("H:i"), $log->intended_time->format("H:i"));
        $this->assertEquals("750 mg", $log->dosage_taken);
        $this->assertEquals("Corrected intended time", $log->notes);
    }

    public function test_timing_calculations_handle_edge_cases(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create(["user_id" => $user->id]);

        // Test exactly 5 minutes late (should still be "on time")
        $fiveMinutesLog = MedicationLog::create([
            "medication_id" => $medication->id,
            "taken_at" => now()->setTime(8, 5, 0),
            "intended_time" => now()->setTime(8, 0, 0),
            "skipped" => false,
        ]);

        $this->assertFalse($fiveMinutesLog->isTakenAtDifferentTime());
        $this->assertEquals("On time", $fiveMinutesLog->getTimeDifference());

        // Test exactly 6 minutes late (should be "late")
        $sixMinutesLog = MedicationLog::create([
            "medication_id" => $medication->id,
            "taken_at" => now()->setTime(8, 6, 0),
            "intended_time" => now()->setTime(8, 0, 0),
            "skipped" => false,
        ]);

        $this->assertTrue($sixMinutesLog->isTakenAtDifferentTime());
        $this->assertEquals("+6 minutes late", $sixMinutesLog->getTimeDifference());

        // Test no intended time (should return null for difference)
        $noIntendedTimeLog = MedicationLog::create([
            "medication_id" => $medication->id,
            "taken_at" => now()->setTime(8, 0, 0),
            "intended_time" => null,
            "skipped" => false,
        ]);

        $this->assertFalse($noIntendedTimeLog->isTakenAtDifferentTime());
        $this->assertNull($noIntendedTimeLog->getTimeDifference());
    }

    public function test_cross_day_timing_calculations(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create(["user_id" => $user->id]);

        $today = Carbon::today();
        $tomorrow = $today->copy()->addDay();

        // Test medication intended for today but taken tomorrow
        $crossDayLog = MedicationLog::create([
            "medication_id" => $medication->id,
            "taken_at" => $tomorrow->setTime(1, 0, 0), // 1 AM next day
            "intended_time" => $today->setTime(23, 0, 0), // 11 PM previous day
            "skipped" => false,
        ]);

        $this->assertTrue($crossDayLog->isTakenAtDifferentTime());

        $timeDiff = $crossDayLog->getTimeDifference();
        $this->assertStringContains("minutes late", $timeDiff);
    }

    public function test_as_needed_medication_with_custom_intended_time(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create([
            "user_id" => $user->id,
            "as_needed" => true,
        ]);

        $symptomTime = now()->setTime(14, 0, 0); // Symptom started at 2 PM
        $takenTime = now()->setTime(14, 45, 0);  // Took medication at 2:45 PM

        $logData = [
            "medication_id" => $medication->id,
            "taken_at" => $takenTime->format("Y-m-d H:i"),
            "intended_time" => $symptomTime->format("Y-m-d H:i"),
            "dosage_taken" => "500 mg",
            "notes" => "Headache started at 2 PM, took medication 45 minutes later",
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("medications.log-taken"),
            $logData,
        );

        $response->assertRedirect();

        $log = MedicationLog::where("medication_id", $medication->id)->first();
        $this->assertNotNull($log->intended_time);
        $this->assertEquals("14:00", $log->intended_time->format("H:i"));
        $this->assertEquals("14:45", $log->taken_at->format("H:i"));
        $this->assertTrue($log->isTakenAtDifferentTime());
        $this->assertEquals("+45 minutes late", $log->getTimeDifference());
    }

    public function test_validation_requires_valid_intended_time_format(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create(["user_id" => $user->id]);

        $logData = [
            "medication_id" => $medication->id,
            "taken_at" => now()->format("Y-m-d H:i"),
            "intended_time" => "invalid-time-format",
            "dosage_taken" => "500 mg",
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("medications.log-taken"),
            $logData,
        );

        $response->assertSessionHasErrors("intended_time");
    }
}
