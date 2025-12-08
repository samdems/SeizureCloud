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
        "dosage_taken",
        "skipped",
        "skip_reason",
        "notes",
    ];

    protected $casts = [
        "taken_at" => "datetime",
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
}
