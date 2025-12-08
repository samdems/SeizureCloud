<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VitalTypeThreshold extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "user_id",
        "vital_type",
        "low_threshold",
        "high_threshold",
        "systolic_low_threshold",
        "systolic_high_threshold",
        "diastolic_low_threshold",
        "diastolic_high_threshold",
        "is_active",
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        "low_threshold" => "float",
        "high_threshold" => "float",
        "systolic_low_threshold" => "float",
        "systolic_high_threshold" => "float",
        "diastolic_low_threshold" => "float",
        "diastolic_high_threshold" => "float",
        "is_active" => "boolean",
    ];

    /**
     * Get the user that owns the vital type threshold.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if a value is too low based on the threshold.
     */
    public function isTooLow(float $value): bool
    {
        return $this->is_active &&
            $this->low_threshold !== null &&
            $value < $this->low_threshold;
    }

    /**
     * Check if a value is too high based on the threshold.
     */
    public function isTooHigh(float $value): bool
    {
        return $this->is_active &&
            $this->high_threshold !== null &&
            $value > $this->high_threshold;
    }

    /**
     * Get the status of a value based on thresholds.
     */
    public function getValueStatus(float $value): string
    {
        if (!$this->is_active) {
            return "normal";
        }

        if ($this->isTooLow($value)) {
            return "too_low";
        }

        if ($this->isTooHigh($value)) {
            return "too_high";
        }

        return "normal";
    }

    /**
     * Get default thresholds for common vital types.
     */
    public static function getDefaultThresholds(): array
    {
        return [
            "Resting BPM" => ["low" => 60, "high" => 100],
            "Blood Pressure" => [
                "systolic_low" => 90,
                "systolic_high" => 140,
                "diastolic_low" => 60,
                "diastolic_high" => 90,
                "low" => 90, // Primary threshold for backwards compatibility
                "high" => 140, // Primary threshold for backwards compatibility
            ],
            "Weight" => ["low" => null, "high" => null], // Weight thresholds are very personal
            "Temperature" => ["low" => 36.1, "high" => 37.5],
            "Oxygen Saturation" => ["low" => 95, "high" => 100],
            "Blood Sugar" => ["low" => 70, "high" => 140],
            "Sleep Hours" => ["low" => 6, "high" => 10],
            "Water Intake (ml)" => ["low" => 1500, "high" => 4000],
            "Steps" => ["low" => 5000, "high" => null],
        ];
    }

    /**
     * Create default thresholds for a user.
     */
    public static function createDefaultsForUser(int $userId): void
    {
        $defaults = self::getDefaultThresholds();

        foreach ($defaults as $vitalType => $thresholds) {
            $data = [
                "low_threshold" => $thresholds["low"],
                "high_threshold" => $thresholds["high"],
                "is_active" => true,
            ];

            // Add blood pressure specific thresholds
            if ($vitalType === "Blood Pressure") {
                $data["systolic_low_threshold"] = $thresholds["systolic_low"];
                $data["systolic_high_threshold"] = $thresholds["systolic_high"];
                $data["diastolic_low_threshold"] = $thresholds["diastolic_low"];
                $data["diastolic_high_threshold"] =
                    $thresholds["diastolic_high"];
            }

            self::updateOrCreate(
                [
                    "user_id" => $userId,
                    "vital_type" => $vitalType,
                ],
                $data,
            );
        }
    }
}
