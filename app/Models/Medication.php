<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "name",
        "dosage",
        "unit",
        "description",
        "prescriber",
        "start_date",
        "end_date",
        "active",
        "as_needed",
        "notes",
    ];

    protected $casts = [
        "start_date" => "date",
        "end_date" => "date",
        "active" => "boolean",
        "as_needed" => "boolean",
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(MedicationSchedule::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(MedicationLog::class);
    }

    public function todayLogs()
    {
        return $this->logs()->whereDate("taken_at", today());
    }
}
