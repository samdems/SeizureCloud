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
}
