<?php

namespace Tests\Feature;

use App\Models\Medication;
use App\Models\MedicationLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsNeededMedicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_log_multiple_as_needed_doses_per_day(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create([
            "user_id" => $user->id,
            "as_needed" => true,
        ]);

        // Log first dose
        $firstLogData = [
            "medication_id" => $medication->id,
            "taken_at" => now()->setTime(9, 0, 0)->format("Y-m-d H:i"),
            "intended_time" => now()->setTime(8, 45, 0)->format("Y-m-d H:i"),
            "dosage_taken" => "500 mg",
            "notes" => "First dose for headache",
        ];

        $response1 = $this->actingAs($user)->postWithCsrf(
            route("medications.log-taken"),
            $firstLogData,
        );

        // Log second dose
        $secondLogData = [
            "medication_id" => $medication->id,
            "taken_at" => now()->setTime(14, 30, 0)->format("Y-m-d H:i"),
            "intended_time" => now()->setTime(14, 15, 0)->format("Y-m-d H:i"),
            "dosage_taken" => "500 mg",
            "notes" => "Second dose for continued pain",
        ];

        $response2 = $this->actingAs($user)->postWithCsrf(
            route("medications.log-taken"),
            $secondLogData,
        );

        // Log third dose
        $thirdLogData = [
            "medication_id" => $medication->id,
            "taken_at" => now()->setTime(20, 0, 0)->format("Y-m-d H:i"),
            "intended_time" => now()->setTime(19, 30, 0)->format("Y-m-d H:i"),
            "dosage_taken" => "250 mg",
            "notes" => "Third dose, reduced amount",
        ];

        $response3 = $this->actingAs($user)->postWithCsrf(
            route("medications.log-taken"),
            $thirdLogData,
        );

        $response1->assertRedirect();
        $response2->assertRedirect();
        $response3->assertRedirect();

        // Check that all three logs were created
        $logs = MedicationLog::where("medication_id", $medication->id)
            ->whereDate("taken_at", today())
            ->orderBy("taken_at")
            ->get();

        $this->assertCount(3, $logs);
        $this->assertEquals("First dose for headache", $logs[0]->notes);
        $this->assertEquals("Second dose for continued pain", $logs[1]->notes);
        $this->assertEquals("Third dose, reduced amount", $logs[2]->notes);
        $this->assertEquals("500 mg", $logs[0]->dosage_taken);
        $this->assertEquals("500 mg", $logs[1]->dosage_taken);
        $this->assertEquals("250 mg", $logs[2]->dosage_taken);
    }

    public function test_as_needed_medications_display_multiple_doses_in_schedule(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create([
            "user_id" => $user->id,
            "as_needed" => true,
            "active" => true,
        ]);

        // Create multiple logs for today
        MedicationLog::create([
            "medication_id" => $medication->id,
            "taken_at" => now()->setTime(9, 0, 0),
            "intended_time" => now()->setTime(8, 45, 0),
            "dosage_taken" => "500 mg",
            "notes" => "Morning headache",
            "skipped" => false,
        ]);

        MedicationLog::create([
            "medication_id" => $medication->id,
            "taken_at" => now()->setTime(14, 30, 0),
            "intended_time" => now()->setTime(14, 15, 0),
            "dosage_taken" => "500 mg",
            "notes" => "Afternoon flare-up",
            "skipped" => false,
        ]);

        $response = $this->actingAs($user)->get(route("medications.schedule"));

        $response->assertStatus(200);
        $response->assertViewIs("medications.schedule");

        // Check that the view data contains the medication entries
        $todaySchedule = $response->viewData("todaySchedule");
        $asNeededItems = collect($todaySchedule)->filter(function ($item) use (
            $medication,
        ) {
            return $item["medication"]->id === $medication->id &&
                $item["as_needed"];
        });

        // Should have 2 taken doses + 1 available for next dose = 3 total
        $this->assertGreaterThanOrEqual(3, $asNeededItems->count());

        // Check that taken items have log data
        $takenItems = $asNeededItems->filter(fn($item) => $item["taken"]);
        $this->assertCount(2, $takenItems);

        // Check that there's still an available option for next dose
        $availableItems = $asNeededItems->filter(fn($item) => !$item["taken"]);
        $this->assertGreaterThanOrEqual(1, $availableItems->count());
    }

    public function test_as_needed_medications_in_schedule_history(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create([
            "user_id" => $user->id,
            "as_needed" => true,
            "active" => true,
        ]);

        $targetDate = now()->subDays(2);

        // Create multiple logs for the target date
        MedicationLog::create([
            "medication_id" => $medication->id,
            "taken_at" => $targetDate->copy()->setTime(10, 30, 0),
            "intended_time" => $targetDate->copy()->setTime(10, 0, 0),
            "dosage_taken" => "500 mg",
            "notes" => "Morning dose",
            "skipped" => false,
        ]);

        MedicationLog::create([
            "medication_id" => $medication->id,
            "taken_at" => $targetDate->copy()->setTime(16, 45, 0),
            "intended_time" => $targetDate->copy()->setTime(16, 30, 0),
            "dosage_taken" => "250 mg",
            "notes" => "Afternoon dose, reduced",
            "skipped" => false,
        ]);

        $response = $this->actingAs($user)->get(
            route("medications.schedule.history", [
                "date" => $targetDate->format("Y-m-d"),
            ]),
        );

        $response->assertStatus(200);
        $response->assertViewIs("medications.schedule-history");

        // Check that the view data contains both doses
        $daySchedule = $response->viewData("daySchedule");
        $asNeededItems = collect($daySchedule)->filter(function ($item) use (
            $medication,
        ) {
            return $item["medication"]->id === $medication->id &&
                $item["as_needed"] &&
                $item["taken"];
        });

        $this->assertCount(2, $asNeededItems);
    }

    public function test_as_needed_medication_without_logs_shows_single_entry(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create([
            "user_id" => $user->id,
            "as_needed" => true,
            "active" => true,
        ]);

        // No logs created for this medication

        $response = $this->actingAs($user)->get(route("medications.schedule"));

        $response->assertStatus(200);
        $todaySchedule = $response->viewData("todaySchedule");
        $asNeededItems = collect($todaySchedule)->filter(function ($item) use (
            $medication,
        ) {
            return $item["medication"]->id === $medication->id &&
                $item["as_needed"];
        });

        // Should have exactly 1 entry (the available one)
        $this->assertCount(1, $asNeededItems);
        $this->assertFalse($asNeededItems->first()["taken"]);
    }

    public function test_as_needed_medication_can_be_skipped(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create([
            "user_id" => $user->id,
            "as_needed" => true,
        ]);

        $intendedTime = now()->setTime(14, 0, 0); // When symptom occurred

        $logData = [
            "medication_id" => $medication->id,
            "intended_time" => $intendedTime->format("Y-m-d H:i"),
            "skip_reason" => "Side effects",
            "notes" => "Decided not to take due to nausea",
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("medications.log-skipped"),
            $logData,
        );

        $response->assertRedirect();
        $this->assertDatabaseHas("medication_logs", [
            "medication_id" => $medication->id,
            "medication_schedule_id" => null,
            "skipped" => true,
            "skip_reason" => "Side effects",
            "intended_time" => $intendedTime->format("Y-m-d H:i:s"),
        ]);
    }

    public function test_as_needed_medication_validation_allows_missing_schedule(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create([
            "user_id" => $user->id,
            "as_needed" => true,
        ]);

        $logData = [
            "medication_id" => $medication->id,
            "taken_at" => now()->format("Y-m-d H:i"),
            "intended_time" => now()->subMinutes(15)->format("Y-m-d H:i"),
            "dosage_taken" => "500 mg",
            "notes" => "Taken for headache",
        ];

        $response = $this->actingAs($user)->postWithCsrf(
            route("medications.log-taken"),
            $logData,
        );

        $response->assertRedirect();
        $this->assertDatabaseHas("medication_logs", [
            "medication_id" => $medication->id,
            "medication_schedule_id" => null,
            "skipped" => false,
        ]);
    }

    public function test_mixed_regular_and_as_needed_medications_in_schedule(): void
    {
        $user = User::factory()->create();

        // Regular scheduled medication
        $regularMed = Medication::factory()->create([
            "user_id" => $user->id,
            "as_needed" => false,
            "active" => true,
        ]);

        $schedule = \App\Models\MedicationSchedule::factory()->create([
            "medication_id" => $regularMed->id,
            "scheduled_time" => "08:00:00",
        ]);

        // As-needed medication
        $asNeededMed = Medication::factory()->create([
            "user_id" => $user->id,
            "as_needed" => true,
            "active" => true,
        ]);

        // Log one as-needed dose
        MedicationLog::create([
            "medication_id" => $asNeededMed->id,
            "taken_at" => now()->setTime(10, 0, 0),
            "intended_time" => now()->setTime(9, 45, 0),
            "dosage_taken" => "500 mg",
            "skipped" => false,
        ]);

        $response = $this->actingAs($user)->get(route("medications.schedule"));

        $response->assertStatus(200);
        $todaySchedule = $response->viewData("todaySchedule");

        // Check regular medication appears
        $regularItems = collect($todaySchedule)->filter(function ($item) use (
            $regularMed,
        ) {
            return $item["medication"]->id === $regularMed->id;
        });
        $this->assertCount(1, $regularItems);
        $this->assertFalse($regularItems->first()["as_needed"]);

        // Check as-needed medication appears (1 taken + 1 available)
        $asNeededItems = collect($todaySchedule)->filter(function ($item) use (
            $asNeededMed,
        ) {
            return $item["medication"]->id === $asNeededMed->id;
        });
        $this->assertGreaterThanOrEqual(2, $asNeededItems->count());
        $this->assertTrue($asNeededItems->first()["as_needed"]);
    }

    public function test_as_needed_medication_dosage_variations(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create([
            "user_id" => $user->id,
            "as_needed" => true,
            "dosage" => "500",
            "unit" => "mg",
        ]);

        // Log doses with different amounts
        $doses = [
            ["dosage" => "250 mg", "time" => "09:00"],
            ["dosage" => "500 mg", "time" => "13:00"],
            ["dosage" => "750 mg", "time" => "17:00"],
        ];

        foreach ($doses as $dose) {
            $logData = [
                "medication_id" => $medication->id,
                "taken_at" => now()->setTimeFromTimeString($dose["time"])->format("Y-m-d H:i"),
                "dosage_taken" => $dose["dosage"],
            ];

            $response = $this->actingAs($user)->postWithCsrf(
                route("medications.log-taken"),
                $logData,
            );
            $response->assertRedirect();
        }

        // Verify all doses were logged with correct amounts
        $logs = MedicationLog::where("medication_id", $medication->id)
            ->whereDate("taken_at", today())
            ->orderBy("taken_at")
            ->get();

        $this->assertCount(3, $logs);
        $this->assertEquals("250 mg", $logs[0]->dosage_taken);
        $this->assertEquals("500 mg", $logs[1]->dosage_taken);
        $this->assertEquals("750 mg", $logs[2]->dosage_taken);
    }

    public function test_as_needed_medication_cross_day_tracking(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create([
            "user_id" => $user->id,
            "as_needed" => true,
        ]);

        $yesterday = now()->subDay();
        $today = now();

        // Log dose yesterday
        MedicationLog::create([
            "medication_id" => $medication->id,
            "taken_at" => $yesterday->setTime(22, 0, 0),
            "dosage_taken" => "500 mg",
            "skipped" => false,
        ]);

        // Log dose today
        MedicationLog::create([
            "medication_id" => $medication->id,
            "taken_at" => $today->setTime(8, 0, 0),
            "dosage_taken" => "500 mg",
            "skipped" => false,
        ]);

        // Check today's schedule only shows today's dose
        $response = $this->actingAs($user)->get(route("medications.schedule"));
        $todaySchedule = $response->viewData("todaySchedule");
        $takenTodayItems = collect($todaySchedule)->filter(function ($item) use (
            $medication,
        ) {
            return $item["medication"]->id === $medication->id &&
                $item["taken"] &&
                isset($item["log"]);
        });

        $this->assertCount(1, $takenTodayItems);
        $takenItem = $takenTodayItems->first();
        $this->assertEquals(
            $today->format("Y-m-d"),
            $takenItem["log"]->taken_at->format("Y-m-d"),
        );

        // Check yesterday's schedule shows yesterday's dose
        $response = $this->actingAs($user)->get(
            route("medications.schedule.history", [
                "date" => $yesterday->format("Y-m-d"),
            ]),
        );
        $daySchedule = $response->viewData("daySchedule");
        $takenYesterdayItems = collect($daySchedule)->filter(function ($item) use (
            $medication,
        ) {
            return $item["medication"]->id === $medication->id &&
                $item["taken"] &&
                isset($item["log"]);
        });

        $this->assertCount(1, $takenYesterdayItems);
        $takenItem = $takenYesterdayItems->first();
        $this->assertEquals(
            $yesterday->format("Y-m-d"),
            $takenItem["log"]->taken_at->format("Y-m-d"),
        );
    }

    public function test_as_needed_medication_with_notes_tracking(): void
    {
        $user = User::factory()->create();
        $medication = Medication::factory()->create([
            "user_id" => $user->id,
            "as_needed" => true,
        ]);

        $scenarios = [
            [
                "time" => "09:00",
                "notes" => "Severe headache - pain level 8/10",
                "dosage" => "500 mg",
            ],
            [
                "time" => "14:00",
                "notes" => "Mild headache returning - pain level 4/10",
                "dosage" => "250 mg",
            ],
            [
                "time" => "19:00",
                "notes" => "Breakthrough pain despite previous doses",
                "dosage" => "500 mg",
            ],
        ];

        foreach ($scenarios as $scenario) {
            $logData = [
                "medication_id" => $medication->id,
                "taken_at" => now()->setTimeFromTimeString($scenario["time"])->format("Y-m-d H:i"),
                "dosage_taken" => $scenario["dosage"],
                "notes" => $scenario["notes"],
            ];

            $response = $this->actingAs($user)->postWithCsrf(
                route("medications.log-taken"),
                $logData,
            );
            $response->assertRedirect();
        }

        // Verify all notes were preserved
        $logs = MedicationLog::where("medication_id", $medication->id)
            ->whereDate("taken_at", today())
            ->orderBy("taken_at")
            ->get();

        $this->assertCount(3, $logs);
        foreach ($scenarios as $index => $scenario) {
            $this->assertEquals($scenario["notes"], $logs[$index]->notes);
            $this->assertEquals($scenario["dosage"], $logs[$index]->dosage_taken);
        }
    }
}
