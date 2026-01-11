<?php

namespace Tests\Unit;

use App\Models\Seizure;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeizureModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_duration_minutes_accessor_converts_seconds_to_minutes(): void
    {
        $seizure = new Seizure();
        $seizure->duration_seconds = 330; // 5 minutes 30 seconds

        $this->assertEquals(5.5, $seizure->duration_minutes);
    }

    public function test_duration_minutes_accessor_returns_null_for_null_seconds(): void
    {
        $seizure = new Seizure();
        $seizure->duration_seconds = null;

        $this->assertNull($seizure->duration_minutes);
    }

    public function test_duration_minutes_accessor_returns_null_for_zero_seconds(): void
    {
        $seizure = new Seizure();
        $seizure->duration_seconds = 0;

        $this->assertNull($seizure->duration_minutes);
    }

    public function test_formatted_duration_accessor_with_minutes_and_seconds(): void
    {
        $seizure = new Seizure();
        $seizure->duration_seconds = 330; // 5 minutes 30 seconds

        $this->assertEquals('5m 30s', $seizure->formatted_duration);
    }

    public function test_formatted_duration_accessor_with_only_minutes(): void
    {
        $seizure = new Seizure();
        $seizure->duration_seconds = 300; // 5 minutes exactly

        $this->assertEquals('5m', $seizure->formatted_duration);
    }

    public function test_formatted_duration_accessor_with_only_seconds(): void
    {
        $seizure = new Seizure();
        $seizure->duration_seconds = 45; // 45 seconds

        $this->assertEquals('45s', $seizure->formatted_duration);
    }

    public function test_formatted_duration_accessor_with_null_duration(): void
    {
        $seizure = new Seizure();
        $seizure->duration_seconds = null;

        $this->assertEquals('Unknown', $seizure->formatted_duration);
    }

    public function test_formatted_duration_accessor_with_zero_duration(): void
    {
        $seizure = new Seizure();
        $seizure->duration_seconds = 0;

        $this->assertEquals('Unknown', $seizure->formatted_duration);
    }

    public function test_calculated_duration_returns_duration_seconds_when_set(): void
    {
        $seizure = new Seizure();
        $seizure->duration_seconds = 330;

        $this->assertEquals(330, $seizure->calculated_duration);
    }

    public function test_calculated_duration_calculates_from_start_end_times(): void
    {
        $startTime = now();
        $endTime = $startTime->copy()->addMinutes(5)->addSeconds(30);

        $seizure = new Seizure();
        $seizure->start_time = $startTime;
        $seizure->end_time = $endTime;
        $seizure->duration_seconds = null;

        // Should calculate 330 seconds (5 minutes 30 seconds)
        $this->assertEquals(330, $seizure->calculated_duration);
    }

    public function test_calculated_duration_prefers_stored_duration_over_calculated(): void
    {
        $startTime = now();
        $endTime = $startTime->copy()->addMinutes(10); // 10 minutes difference

        $seizure = new Seizure();
        $seizure->start_time = $startTime;
        $seizure->end_time = $endTime;
        $seizure->duration_seconds = 330; // But stored duration is 5.5 minutes

        // Should prefer stored duration over calculated
        $this->assertEquals(330, $seizure->calculated_duration);
    }

    public function test_calculated_duration_returns_null_when_no_data(): void
    {
        $seizure = new Seizure();
        $seizure->duration_seconds = null;
        $seizure->start_time = null;
        $seizure->end_time = null;

        $this->assertNull($seizure->calculated_duration);
    }

    public function test_calculated_duration_returns_null_when_missing_end_time(): void
    {
        $seizure = new Seizure();
        $seizure->duration_seconds = null;
        $seizure->start_time = now();
        $seizure->end_time = null;

        $this->assertNull($seizure->calculated_duration);
    }

    public function test_set_duration_from_minutes_and_seconds(): void
    {
        $seizure = new Seizure();
        $seizure->setDurationFromMinutesAndSeconds(7, 45);

        $this->assertEquals(465, $seizure->duration_seconds); // 7 * 60 + 45
    }

    public function test_set_duration_from_minutes_only(): void
    {
        $seizure = new Seizure();
        $seizure->setDurationFromMinutesAndSeconds(5, 0);

        $this->assertEquals(300, $seizure->duration_seconds); // 5 * 60
    }

    public function test_set_duration_from_seconds_only(): void
    {
        $seizure = new Seizure();
        $seizure->setDurationFromMinutesAndSeconds(0, 45);

        $this->assertEquals(45, $seizure->duration_seconds);
    }

    public function test_set_duration_with_zero_values(): void
    {
        $seizure = new Seizure();
        $seizure->setDurationFromMinutesAndSeconds(0, 0);

        $this->assertEquals(0, $seizure->duration_seconds);
    }

    public function test_model_casts_duration_seconds_as_integer(): void
    {
        $seizure = Seizure::factory()->make([
            'duration_seconds' => '330'
        ]);

        $this->assertIsInt($seizure->duration_seconds);
        $this->assertEquals(330, $seizure->duration_seconds);
    }

    public function test_model_handles_null_duration_seconds(): void
    {
        $seizure = Seizure::factory()->make([
            'duration_seconds' => null
        ]);

        $this->assertNull($seizure->duration_seconds);
        $this->assertNull($seizure->duration_minutes);
        $this->assertEquals('Unknown', $seizure->formatted_duration);
    }

    public function test_duration_seconds_is_fillable(): void
    {
        $seizure = new Seizure();
        $fillable = $seizure->getFillable();

        $this->assertContains('duration_seconds', $fillable);
        $this->assertNotContains('duration_minutes', $fillable);
    }

    public function test_edge_case_very_long_duration(): void
    {
        $seizure = new Seizure();
        $seizure->duration_seconds = 7200; // 2 hours (120 minutes)

        $this->assertEquals(120.0, $seizure->duration_minutes);
        $this->assertEquals('120m', $seizure->formatted_duration);
    }

    public function test_edge_case_very_short_duration(): void
    {
        $seizure = new Seizure();
        $seizure->duration_seconds = 1; // 1 second

        $this->assertEquals(0.0, $seizure->duration_minutes); // Rounds to 0
        $this->assertEquals('1s', $seizure->formatted_duration);
    }

    public function test_duration_accessor_rounds_to_one_decimal_place(): void
    {
        $seizure = new Seizure();
        $seizure->duration_seconds = 333; // 5 minutes 33 seconds = 5.55 minutes

        $this->assertEquals(5.6, $seizure->duration_minutes); // Should round to 5.6
    }

    public function test_formatted_duration_handles_large_numbers(): void
    {
        $seizure = new Seizure();
        $seizure->duration_seconds = 3661; // 61 minutes 1 second

        $this->assertEquals('61m 1s', $seizure->formatted_duration);
    }

    public function test_calculated_duration_handles_microseconds_in_time_diff(): void
    {
        $startTime = now();
        $endTime = $startTime->copy()->addSeconds(90)->addMicroseconds(500000); // 90.5 seconds

        $seizure = new Seizure();
        $seizure->start_time = $startTime;
        $seizure->end_time = $endTime;
        $seizure->duration_seconds = null;

        // Should calculate to 90 seconds (floor of 90.5)
        $this->assertEquals(90, $seizure->calculated_duration);
    }

    public function test_model_relationships_are_preserved(): void
    {
        $user = User::factory()->create();
        $seizure = Seizure::factory()->create([
            'user_id' => $user->id,
            'duration_seconds' => 300
        ]);

        $this->assertInstanceOf(User::class, $seizure->user);
        $this->assertEquals($user->id, $seizure->user->id);
    }
}
