<?php

namespace Tests\Feature;

use App\Models\Seizure;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SeizureDurationMigrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // We need to manually create the old table structure for testing
        $this->createOldTableStructure();
    }

    private function createOldTableStructure(): void
    {
        // Create seizures table with old duration_minutes column
        Schema::create("seizures_old", function ($table) {
            $table->id();
            $table->foreignId("user_id")->constrained()->onDelete("cascade");
            $table->dateTime("start_time");
            $table->dateTime("end_time")->nullable();
            $table->integer("duration_minutes")->nullable();
            $table->tinyInteger("severity");
            $table->boolean("on_period")->default(false);
            $table
                ->enum("nhs_contact_type", [
                    "GP",
                    "111",
                    "999",
                    "Epileptic Specialist",
                ])
                ->nullable();
            $table->dateTime("postictal_state_end")->nullable();
            $table->boolean("ambulance_called")->default(false);
            $table->boolean("slept_after")->default(false);
            $table->text("notes")->nullable();
            $table->timestamps();
        });
    }

    public function test_migration_converts_minutes_to_seconds(): void
    {
        $user = User::factory()->create();

        // Insert test data with old structure
        DB::table("seizures_old")->insert([
            "user_id" => $user->id,
            "start_time" => now()->subHours(2),
            "end_time" => now()->subHours(1),
            "duration_minutes" => 5,
            "severity" => 7,
            "created_at" => now(),
            "updated_at" => now(),
        ]);

        DB::table("seizures_old")->insert([
            "user_id" => $user->id,
            "start_time" => now()->subHours(4),
            "end_time" => now()->subHours(3),
            "duration_minutes" => 10,
            "severity" => 6,
            "created_at" => now(),
            "updated_at" => now(),
        ]);

        DB::table("seizures_old")->insert([
            "user_id" => $user->id,
            "start_time" => now()->subHours(6),
            "end_time" => now()->subHours(5),
            "duration_minutes" => null,
            "severity" => 5,
            "created_at" => now(),
            "updated_at" => now(),
        ]);

        // Simulate the migration conversion
        $this->runMigrationConversion();

        // Check that data was converted correctly
        $seizures = DB::table("seizures_old")->get();

        $this->assertEquals(300, $seizures[0]->duration_minutes); // 5 * 60
        $this->assertEquals(600, $seizures[1]->duration_minutes); // 10 * 60
        $this->assertNull($seizures[2]->duration_minutes);
    }

    public function test_migration_handles_zero_duration(): void
    {
        $user = User::factory()->create();

        DB::table("seizures_old")->insert([
            "user_id" => $user->id,
            "start_time" => now()->subHours(2),
            "end_time" => now()->subHours(1),
            "duration_minutes" => 0,
            "severity" => 7,
            "created_at" => now(),
            "updated_at" => now(),
        ]);

        $this->runMigrationConversion();

        $seizure = DB::table("seizures_old")->first();
        $this->assertEquals(0, $seizure->duration_minutes);
    }

    public function test_migration_handles_large_durations(): void
    {
        $user = User::factory()->create();

        DB::table("seizures_old")->insert([
            "user_id" => $user->id,
            "start_time" => now()->subHours(3),
            "end_time" => now()->subHours(1),
            "duration_minutes" => 120, // 2 hours
            "severity" => 9,
            "created_at" => now(),
            "updated_at" => now(),
        ]);

        $this->runMigrationConversion();

        $seizure = DB::table("seizures_old")->first();
        $this->assertEquals(7200, $seizure->duration_minutes); // 120 * 60 = 7200 seconds
    }

    public function test_migration_preserves_other_data(): void
    {
        $user = User::factory()->create();

        $originalData = [
            "user_id" => $user->id,
            "start_time" => now()->subHours(2),
            "end_time" => now()->subHours(1),
            "duration_minutes" => 8,
            "severity" => 6,
            "on_period" => true,
            "nhs_contact_type" => "111",
            "ambulance_called" => true,
            "slept_after" => false,
            "notes" => "Test seizure notes",
            "created_at" => now(),
            "updated_at" => now(),
        ];

        DB::table("seizures_old")->insert($originalData);

        $this->runMigrationConversion();

        $seizure = DB::table("seizures_old")->first();

        // Check that other fields are preserved
        $this->assertEquals($user->id, $seizure->user_id);
        $this->assertEquals(6, $seizure->severity);
        $this->assertEquals(1, $seizure->on_period);
        $this->assertEquals("111", $seizure->nhs_contact_type);
        $this->assertEquals(1, $seizure->ambulance_called);
        $this->assertEquals(0, $seizure->slept_after);
        $this->assertEquals("Test seizure notes", $seizure->notes);

        // Check that duration was converted
        $this->assertEquals(480, $seizure->duration_minutes); // 8 * 60
    }

    public function test_rollback_migration_converts_seconds_back_to_minutes(): void
    {
        $user = User::factory()->create();

        // Insert data as if migration has run (duration in seconds)
        DB::table("seizures_old")->insert([
            "user_id" => $user->id,
            "start_time" => now()->subHours(2),
            "end_time" => now()->subHours(1),
            "duration_minutes" => 330, // 5.5 minutes worth of seconds
            "severity" => 7,
            "created_at" => now(),
            "updated_at" => now(),
        ]);

        DB::table("seizures_old")->insert([
            "user_id" => $user->id,
            "start_time" => now()->subHours(4),
            "end_time" => now()->subHours(3),
            "duration_minutes" => 605, // 10.08 minutes worth of seconds
            "severity" => 6,
            "created_at" => now(),
            "updated_at" => now(),
        ]);

        // Simulate rollback conversion (seconds back to minutes)
        $this->runRollbackConversion();

        $seizures = DB::table("seizures_old")->get();

        // Should round to nearest minute
        $this->assertEquals(5, $seizures[0]->duration_minutes); // 330/60 = 5.5, rounds to 5
        $this->assertEquals(10, $seizures[1]->duration_minutes); // 605/60 = 10.08, rounds to 10
    }

    public function test_rollback_handles_null_values(): void
    {
        $user = User::factory()->create();

        DB::table("seizures_old")->insert([
            "user_id" => $user->id,
            "start_time" => now()->subHours(2),
            "end_time" => now()->subHours(1),
            "duration_minutes" => null,
            "severity" => 7,
            "created_at" => now(),
            "updated_at" => now(),
        ]);

        $this->runRollbackConversion();

        $seizure = DB::table("seizures_old")->first();
        $this->assertNull($seizure->duration_minutes);
    }

    public function test_rollback_handles_zero_values(): void
    {
        $user = User::factory()->create();

        DB::table("seizures_old")->insert([
            "user_id" => $user->id,
            "start_time" => now()->subHours(2),
            "end_time" => now()->subHours(1),
            "duration_minutes" => 0,
            "severity" => 7,
            "created_at" => now(),
            "updated_at" => now(),
        ]);

        $this->runRollbackConversion();

        $seizure = DB::table("seizures_old")->first();
        $this->assertEquals(0, $seizure->duration_minutes);
    }

    public function test_migration_preserves_data_integrity(): void
    {
        $user = User::factory()->create();

        // Create multiple seizures with various durations
        $testData = [
            ["duration_minutes" => 1, "expected_seconds" => 60],
            ["duration_minutes" => 5, "expected_seconds" => 300],
            ["duration_minutes" => 15, "expected_seconds" => 900],
            ["duration_minutes" => 30, "expected_seconds" => 1800],
            ["duration_minutes" => 60, "expected_seconds" => 3600],
            ["duration_minutes" => null, "expected_seconds" => null],
        ];

        foreach ($testData as $index => $data) {
            DB::table("seizures_old")->insert([
                "user_id" => $user->id,
                "start_time" => now()->subHours(2 + $index),
                "end_time" => now()->subHours(1 + $index),
                "duration_minutes" => $data["duration_minutes"],
                "severity" => 5,
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }

        $countBefore = DB::table("seizures_old")->count();

        $this->runMigrationConversion();

        $countAfter = DB::table("seizures_old")->count();
        $seizures = DB::table("seizures_old")->orderBy("id")->get();

        // Ensure no data loss
        $this->assertEquals($countBefore, $countAfter);

        // Check each conversion
        foreach ($testData as $index => $data) {
            $this->assertEquals(
                $data["expected_seconds"],
                $seizures[$index]->duration_minutes,
                "Failed for index {$index}: expected {$data["expected_seconds"]}, got {$seizures[$index]->duration_minutes}",
            );
        }
    }

    public function test_migration_column_rename_simulation(): void
    {
        $user = User::factory()->create();

        DB::table("seizures_old")->insert([
            "user_id" => $user->id,
            "start_time" => now()->subHours(2),
            "end_time" => now()->subHours(1),
            "duration_minutes" => 7,
            "severity" => 8,
            "created_at" => now(),
            "updated_at" => now(),
        ]);

        // Step 1: Convert minutes to seconds
        $this->runMigrationConversion();

        // Step 2: Simulate column rename by adding new column and copying data
        Schema::table("seizures_old", function ($table) {
            $table->integer("duration_seconds")->nullable();
        });

        DB::statement(
            "UPDATE seizures_old SET duration_seconds = duration_minutes",
        );

        // Check the new column has the correct data
        $seizure = DB::table("seizures_old")->first();
        $this->assertEquals(420, $seizure->duration_seconds); // 7 * 60
        $this->assertEquals(420, $seizure->duration_minutes); // Should be the same
    }

    private function runMigrationConversion(): void
    {
        // Simulate the migration's up() method conversion
        DB::statement(
            "UPDATE seizures_old SET duration_minutes = duration_minutes * 60 WHERE duration_minutes IS NOT NULL",
        );
    }

    private function runRollbackConversion(): void
    {
        // Simulate the migration's down() method conversion
        DB::statement(
            "UPDATE seizures_old SET duration_minutes = ROUND(duration_minutes / 60) WHERE duration_minutes IS NOT NULL",
        );
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists("seizures_old");
        parent::tearDown();
    }
}
