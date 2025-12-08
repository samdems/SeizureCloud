<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicationLogTakenRequest extends FormRequest
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
            "medication_id" => "required|exists:medications,id",
            "medication_schedule_id" =>
                "nullable|exists:medication_schedules,id",
            "taken_at" => "required|date",
            "intended_time" => "nullable|date",
            "dosage_taken" => "nullable|string|max:255",
            "notes" => "nullable|string|max:1000",
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
            "medication_id.required" => "Please select a medication.",
            "medication_id.exists" => "The selected medication is invalid.",
            "medication_schedule_id.exists" =>
                "The selected medication schedule is invalid.",
            "taken_at.required" =>
                "Please specify when the medication was taken.",
            "taken_at.date" => "The taken time must be a valid date and time.",
            "intended_time.date" =>
                "The intended time must be a valid date and time.",
            "dosage_taken.max" => "Dosage taken cannot exceed 255 characters.",
            "notes.max" => "Notes cannot exceed 1000 characters.",
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
            "medication_id" => "medication",
            "medication_schedule_id" => "medication schedule",
            "taken_at" => "taken time",
            "intended_time" => "intended time",
            "dosage_taken" => "dosage taken",
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure medication_schedule_id is null if empty
        if ($this->medication_schedule_id === "") {
            $this->merge([
                "medication_schedule_id" => null,
            ]);
        }
    }
}
