<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vital extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "user_id",
        "type",
        "value",
        "recorded_at",
        "notes",
        "low_threshold",
        "high_threshold",
        "status",
        "systolic_value",
        "diastolic_value",
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        "recorded_at" => "datetime",
        "low_threshold" => "float",
        "high_threshold" => "float",
        "systolic_value" => "float",
        "diastolic_value" => "float",
    ];

    /**
     * Get the user that owns the vital record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the threshold configuration for this vital type and user.
     */
    public function typeThreshold()
    {
        return $this->belongsTo(
            VitalTypeThreshold::class,
            "type",
            "vital_type",
        )->where("user_id", $this->user_id);
    }

    /**
     * Check if this vital value is too low.
     */
    public function isTooLow(): bool
    {
        $threshold = $this->low_threshold ?? $this->getDefaultLowThreshold();
        return $threshold !== null && $this->value < $threshold;
    }

    /**
     * Check if this vital value is too high.
     */
    public function isTooHigh(): bool
    {
        $threshold = $this->high_threshold ?? $this->getDefaultHighThreshold();
        return $threshold !== null && $this->value > $threshold;
    }

    /**
     * Update the status based on current thresholds.
     */
    public function updateStatus(): void
    {
        $this->status = $this->getStatus();
    }

    /**
     * Get default low threshold for this vital type from user's settings or defaults.
     */
    public function getDefaultLowThreshold(): ?float
    {
        $userThreshold = VitalTypeThreshold::where("user_id", $this->user_id)
            ->where("vital_type", $this->type)
            ->where("is_active", true)
            ->first();

        if ($userThreshold) {
            return $userThreshold->low_threshold;
        }

        $defaults = VitalTypeThreshold::getDefaultThresholds();
        return $defaults[$this->type]["low"] ?? null;
    }

    /**
     * Get default high threshold for this vital type from user's settings or defaults.
     */
    public function getDefaultHighThreshold(): ?float
    {
        $userThreshold = VitalTypeThreshold::where("user_id", $this->user_id)
            ->where("vital_type", $this->type)
            ->where("is_active", true)
            ->first();

        if ($userThreshold) {
            return $userThreshold->high_threshold;
        }

        $defaults = VitalTypeThreshold::getDefaultThresholds();
        return $defaults[$this->type]["high"] ?? null;
    }

    /**
     * Get the status badge class for styling.
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->status ?? $this->getStatus()) {
            "too_low" => "badge-error",
            "too_high" => "badge-error",
            default => "badge-success",
        };
    }

    /**
     * Check if this vital is a blood pressure reading.
     */
    public function isBloodPressure(): bool
    {
        return $this->type === "Blood Pressure";
    }

    /**
     * Parse blood pressure value from formats like "120/80".
     */
    public static function parseBloodPressure(string $value): array
    {
        // Remove any spaces and check for slash
        $cleaned = str_replace(" ", "", $value);

        if (strpos($cleaned, "/") !== false) {
            $parts = explode("/", $cleaned);
            if (
                count($parts) === 2 &&
                is_numeric($parts[0]) &&
                is_numeric($parts[1])
            ) {
                return [
                    "systolic" => (float) $parts[0],
                    "diastolic" => (float) $parts[1],
                    "combined_value" => (float) $parts[0], // Use systolic as primary value
                ];
            }
        }

        // If not in BP format, treat as regular value
        return [
            "systolic" => null,
            "diastolic" => null,
            "combined_value" => (float) $value,
        ];
    }

    /**
     * Get formatted blood pressure display value.
     */
    public function getFormattedValue(): string
    {
        if (
            $this->isBloodPressure() &&
            $this->systolic_value &&
            $this->diastolic_value
        ) {
            return $this->systolic_value . "/" . $this->diastolic_value;
        }

        return (string) $this->value;
    }

    /**
     * Get blood pressure status based on both systolic and diastolic values.
     */
    public function getBloodPressureStatus(): string
    {
        if (
            !$this->isBloodPressure() ||
            !$this->systolic_value ||
            !$this->diastolic_value
        ) {
            // Use regular vital status for non-BP or incomplete BP readings
            if ($this->isTooLow()) {
                return "too_low";
            }
            if ($this->isTooHigh()) {
                return "too_high";
            }
            return "normal";
        }

        $bpThresholds = VitalTypeThreshold::where("user_id", $this->user_id)
            ->where("vital_type", "Blood Pressure")
            ->where("is_active", true)
            ->first();

        // Use defaults if no user thresholds
        if (!$bpThresholds) {
            $defaults = VitalTypeThreshold::getDefaultThresholds();
            $systolicLow = $defaults["Blood Pressure"]["systolic_low"] ?? null;
            $systolicHigh =
                $defaults["Blood Pressure"]["systolic_high"] ?? null;
            $diastolicLow =
                $defaults["Blood Pressure"]["diastolic_low"] ?? null;
            $diastolicHigh =
                $defaults["Blood Pressure"]["diastolic_high"] ?? null;
        } else {
            $systolicLow = $bpThresholds->systolic_low_threshold;
            $systolicHigh = $bpThresholds->systolic_high_threshold;
            $diastolicLow = $bpThresholds->diastolic_low_threshold;
            $diastolicHigh = $bpThresholds->diastolic_high_threshold;
        }

        $systolicTooLow = $systolicLow && $this->systolic_value < $systolicLow;
        $systolicTooHigh =
            $systolicHigh && $this->systolic_value > $systolicHigh;
        $diastolicTooLow =
            $diastolicLow && $this->diastolic_value < $diastolicLow;
        $diastolicTooHigh =
            $diastolicHigh && $this->diastolic_value > $diastolicHigh;

        if ($systolicTooLow || $diastolicTooLow) {
            return "too_low";
        }

        if ($systolicTooHigh || $diastolicTooHigh) {
            return "too_high";
        }

        return "normal";
    }

    /**
     * Get detailed blood pressure status text.
     */
    public function getBloodPressureStatusText(): string
    {
        if (
            !$this->isBloodPressure() ||
            !$this->systolic_value ||
            !$this->diastolic_value
        ) {
            return $this->getStatusText();
        }

        $status = $this->getBloodPressureStatus();

        if ($status === "normal") {
            return "Normal BP";
        }

        // Check individual components for detailed status
        $bpThresholds = VitalTypeThreshold::where("user_id", $this->user_id)
            ->where("vital_type", "Blood Pressure")
            ->where("is_active", true)
            ->first();

        // Use defaults if no user thresholds
        $defaults = VitalTypeThreshold::getDefaultThresholds();
        $systolicLow =
            $bpThresholds?->systolic_low_threshold ??
            $defaults["Blood Pressure"]["systolic_low"];
        $systolicHigh =
            $bpThresholds?->systolic_high_threshold ??
            $defaults["Blood Pressure"]["systolic_high"];
        $diastolicLow =
            $bpThresholds?->diastolic_low_threshold ??
            $defaults["Blood Pressure"]["diastolic_low"];
        $diastolicHigh =
            $bpThresholds?->diastolic_high_threshold ??
            $defaults["Blood Pressure"]["diastolic_high"];

        $systolicTooLow = $systolicLow && $this->systolic_value < $systolicLow;
        $systolicTooHigh =
            $systolicHigh && $this->systolic_value > $systolicHigh;
        $diastolicTooLow =
            $diastolicLow && $this->diastolic_value < $diastolicLow;
        $diastolicTooHigh =
            $diastolicHigh && $this->diastolic_value > $diastolicHigh;

        if ($systolicTooLow && $diastolicTooLow) {
            return "Both Too Low";
        } elseif ($systolicTooHigh && $diastolicTooHigh) {
            return "Both Too High";
        } elseif ($systolicTooLow) {
            return "Low Systolic";
        } elseif ($systolicTooHigh) {
            return "High Systolic";
        } elseif ($diastolicTooLow) {
            return "Low Diastolic";
        } elseif ($diastolicTooHigh) {
            return "High Diastolic";
        }

        return "Abnormal BP";
    }

    /**
     * Get the status of this vital (normal, too_low, too_high).
     */
    public function getStatus(): string
    {
        if ($this->isBloodPressure()) {
            return $this->getBloodPressureStatus();
        }

        if ($this->isTooLow()) {
            return "too_low";
        }

        if ($this->isTooHigh()) {
            return "too_high";
        }

        return "normal";
    }

    /**
     * Get the status display text.
     */
    public function getStatusText(): string
    {
        if ($this->isBloodPressure()) {
            return $this->getBloodPressureStatusText();
        }

        return match ($this->status ?? $this->getStatus()) {
            "too_low" => "Too Low",
            "too_high" => "Too High",
            default => "Normal",
        };
    }
}
