<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicationSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        "medication_id",
        "scheduled_time",
        "dosage_multiplier",
        "unit",
        "days_of_week",
        "frequency",
        "active",
        "notes",
    ];

    protected $casts = [
        "scheduled_time" => "datetime:H:i",
        "days_of_week" => "array",
        "active" => "boolean",
    ];

    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(MedicationLog::class);
    }

    public function isScheduledForToday(): bool
    {
        if ($this->frequency === "as_needed") {
            return false;
        }

        if ($this->frequency === "daily" || empty($this->days_of_week)) {
            return true;
        }

        return in_array(now()->dayOfWeek, $this->days_of_week);
    }

    public function getCalculatedDosage(): ?string
    {
        if (!$this->dosage_multiplier) {
            return null;
        }

        return number_format(floatval($this->dosage_multiplier), 2, ".", "");
    }

    public function getCalculatedDosageWithUnit(): ?string
    {
        $dosage = $this->getCalculatedDosage();
        if (!$dosage) {
            return null;
        }

        $unit = $this->unit ?? $this->medication->unit;
        return $dosage . " " . $unit;
    }

    /**
     * Check if this medication is currently due (past scheduled time but not taken)
     */
    public function isDue($date = null): bool
    {
        $checkDate = $date ? \Carbon\Carbon::parse($date) : now();

        if ($date && !$this->isScheduledForDay($checkDate)) {
            return false;
        } elseif (!$date && !$this->isScheduledForToday()) {
            return false;
        }

        // For historical dates, only check if it's today
        if ($date && !$checkDate->isToday()) {
            return false;
        }

        $scheduledTime = $checkDate->copy()->setTimeFrom($this->scheduled_time);
        $now = now();

        // Due if current time is past scheduled time
        return $now->greaterThan($scheduledTime);
    }

    /**
     * Check if this medication is overdue (past scheduled time + grace period)
     */
    public function isOverdue($date = null): bool
    {
        $checkDate = $date ? \Carbon\Carbon::parse($date) : now();

        if ($date && !$this->isScheduledForDay($checkDate)) {
            return false;
        } elseif (!$date && !$this->isScheduledForToday()) {
            return false;
        }

        // For historical dates, only check if it's today
        if ($date && !$checkDate->isToday()) {
            return false;
        }

        $scheduledTime = $checkDate->copy()->setTimeFrom($this->scheduled_time);
        $now = now();
        $overdueThreshold = $scheduledTime->copy()->addMinutes(30);

        // Overdue if current time is past scheduled time + grace period
        return $now->greaterThan($overdueThreshold);
    }

    /**
     * Check if this medication is scheduled for a specific day
     */
    public function isScheduledForDay(\Carbon\Carbon $date): bool
    {
        if ($this->frequency === "as_needed") {
            return false;
        }

        if ($this->frequency === "daily" || empty($this->days_of_week)) {
            return true;
        }

        return in_array($date->dayOfWeek, $this->days_of_week);
    }
}
