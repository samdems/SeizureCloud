<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Seizure extends Model
{
    use HasFactory;
    protected $fillable = [
        "user_id",
        "start_time",
        "end_time",
        "duration_seconds",
        "severity",
        "seizure_type",
        "has_video_evidence",
        "video_notes",
        "video_file_path",
        "video_public_token",
        "video_expires_at",
        "triggers",
        "other_triggers",
        "pre_ictal_symptoms",
        "pre_ictal_notes",
        "recovery_time",
        "post_ictal_confusion",
        "post_ictal_headache",
        "recovery_notes",
        "on_period",
        "days_since_period",
        "medication_adherence",
        "recent_medication_change",
        "experiencing_side_effects",
        "medication_notes",
        "wellbeing_rating",
        "sleep_quality",
        "wellbeing_notes",
        "nhs_contact_type",
        "postictal_state_end",
        "ambulance_called",
        "slept_after",
        "notes",
    ];

    protected $casts = [
        "start_time" => "datetime",
        "end_time" => "datetime",
        "postictal_state_end" => "datetime",
        "video_expires_at" => "datetime",
        "on_period" => "boolean",
        "ambulance_called" => "boolean",
        "slept_after" => "boolean",
        "severity" => "integer",
        "duration_seconds" => "integer",
        "days_since_period" => "integer",
        "has_video_evidence" => "boolean",
        "post_ictal_confusion" => "boolean",
        "post_ictal_headache" => "boolean",
        "recent_medication_change" => "boolean",
        "experiencing_side_effects" => "boolean",
        "triggers" => "array",
        "pre_ictal_symptoms" => "array",
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getCalculatedDurationAttribute(): ?int
    {
        if ($this->duration_seconds) {
            return $this->duration_seconds;
        }

        if ($this->start_time && $this->end_time) {
            return $this->start_time->diffInSeconds($this->end_time);
        }

        return null;
    }

    /**
     * Get duration in minutes (for backward compatibility and display)
     */
    public function getDurationMinutesAttribute(): ?float
    {
        if (!$this->duration_seconds) {
            return null;
        }

        return round($this->duration_seconds / 60, 1);
    }

    /**
     * Get duration formatted as minutes and seconds for display
     */
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration_seconds) {
            return "Unknown";
        }

        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;

        if ($minutes > 0 && $seconds > 0) {
            return "{$minutes}m {$seconds}s";
        } elseif ($minutes > 0) {
            return "{$minutes}m";
        } else {
            return "{$seconds}s";
        }
    }

    /**
     * Set duration from minutes and seconds
     */
    public function setDurationFromMinutesAndSeconds(
        int $minutes = 0,
        int $seconds = 0,
    ): void {
        $this->duration_seconds = $minutes * 60 + $seconds;
    }

    public function hasValidVideo(): bool
    {
        return $this->video_file_path && $this->video_public_token;
    }

    public function getVideoPublicUrl(): ?string
    {
        if (!$this->hasValidVideo()) {
            return null;
        }

        return route("seizures.video.view", [
            "token" => $this->video_public_token,
        ]);
    }
}
