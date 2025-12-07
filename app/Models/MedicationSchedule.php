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
        if (!$this->medication->dosage) {
            return null;
        }

        $calculated =
            floatval($this->medication->dosage) *
            floatval($this->dosage_multiplier);
        return number_format($calculated, 2, ".", "");
    }

    public function getCalculatedDosageWithUnit(): ?string
    {
        $dosage = $this->getCalculatedDosage();
        if (!$dosage) {
            return null;
        }

        return $dosage . " " . $this->medication->unit;
    }
}
