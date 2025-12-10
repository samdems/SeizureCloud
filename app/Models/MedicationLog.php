<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        "medication_id",
        "medication_schedule_id",
        "taken_at",
        "intended_time",
        "dosage_taken",
        "skipped",
        "skip_reason",
        "notes",
    ];

    protected $casts = [
        "taken_at" => "datetime",
        "intended_time" => "datetime",
        "skipped" => "boolean",
    ];

    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(
            MedicationSchedule::class,
            "medication_schedule_id",
        );
    }

    /**
     * Check if this medication was taken late (more than 30 minutes after scheduled time)
     */
    public function isTakenLate(): bool
    {
        // If no schedule or skipped, not considered late
        if (!$this->schedule || $this->skipped) {
            return false;
        }

        $scheduledTime = $this->taken_at
            ->copy()
            ->setTimeFrom($this->schedule->scheduled_time);
        $allowableLateness = 30; // 30 minutes grace period

        return $this->taken_at->greaterThan(
            $scheduledTime->addMinutes($allowableLateness),
        );
    }

    /**
     * Check if this medication was taken at a different time than intended
     */
    public function isTakenAtDifferentTime(): bool
    {
        if (!$this->intended_time || $this->skipped) {
            return false;
        }

        // Allow 5 minute difference for minor timing variations
        $timeDifference = abs(
            $this->taken_at->diffInMinutes($this->intended_time),
        );

        return $timeDifference > 5;
    }

    /**
     * Get the time difference between when medication was intended vs taken
     */
    public function getTimeDifference(): ?string
    {
        if (!$this->intended_time || $this->skipped) {
            return null;
        }

        $minutes = $this->taken_at->diffInMinutes($this->intended_time, false);
        if (abs($minutes) <= 10) {
            return "On time";
        }

        if ($minutes < 0) {
            return abs($minutes) . " minutes late";
        }

        return abs($minutes) . " minutes early";
    }

    /**
     * Get a human-readable description of when this was taken relative to intended time
     */
    public function getTakenTimeDescription(): string
    {
        if ($this->skipped) {
            return "Skipped";
        }

        if ($this->intended_time) {
            $difference = $this->getTimeDifference();
            if ($difference === "On time") {
                return "Taken on time at " . $this->taken_at->format("g:i A");
            }
            return "Taken at " .
                $this->taken_at->format("g:i A") .
                " (" .
                $difference .
                ")";
        }

        return "Taken at " . $this->taken_at->format("g:i A");
    }
}
