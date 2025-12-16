<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmergencySettingsUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "status_epilepticus_duration_minutes" =>
                "required|integer|min:1|max:60",
            "emergency_seizure_count" => "required|integer|min:2|max:10",
            "emergency_seizure_timeframe_hours" =>
                "required|integer|min:1|max:24",
            "emergency_contact_info" => "nullable|string|max:1000",
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            "status_epilepticus_duration_minutes.required" =>
                "Please specify the duration for possible status epilepticus.",
            "status_epilepticus_duration_minutes.integer" =>
                "Duration must be a whole number of minutes.",
            "status_epilepticus_duration_minutes.min" =>
                "Duration must be at least 1 minute.",
            "status_epilepticus_duration_minutes.max" =>
                "Duration cannot exceed 60 minutes.",
            "emergency_seizure_count.required" =>
                "Please specify the seizure count for emergency.",
            "emergency_seizure_count.integer" =>
                "Seizure count must be a whole number.",
            "emergency_seizure_count.min" =>
                "Seizure count must be at least 2.",
            "emergency_seizure_count.max" => "Seizure count cannot exceed 10.",
            "emergency_seizure_timeframe_hours.required" =>
                "Please specify the timeframe for emergency seizures.",
            "emergency_seizure_timeframe_hours.integer" =>
                "Timeframe must be a whole number of hours.",
            "emergency_seizure_timeframe_hours.min" =>
                "Timeframe must be at least 1 hour.",
            "emergency_seizure_timeframe_hours.max" =>
                "Timeframe cannot exceed 24 hours.",
            "emergency_contact_info.max" =>
                "Emergency contact information cannot exceed 1000 characters.",
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            "status_epilepticus_duration_minutes" =>
                "possible status epilepticus duration",
            "emergency_seizure_count" => "emergency seizure count",
            "emergency_seizure_timeframe_hours" =>
                "emergency seizure timeframe",
            "emergency_contact_info" => "emergency contact information",
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Trim whitespace from emergency contact info
        if ($this->has("emergency_contact_info")) {
            $this->merge([
                "emergency_contact_info" => trim(
                    $this->emergency_contact_info ?? "",
                ),
            ]);
        }

        // Set empty emergency contact info to null
        if (empty($this->emergency_contact_info)) {
            $this->merge([
                "emergency_contact_info" => null,
            ]);
        }
    }
}
