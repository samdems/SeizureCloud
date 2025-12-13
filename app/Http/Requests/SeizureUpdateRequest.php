<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SeizureUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled by the controller's authorize() method
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
            "start_time" => "required|date",
            "end_time" => "nullable|date|after:start_time",
            "duration_minutes" => "nullable|integer|min:0",
            "severity" => "required|integer|min:1|max:10",
            "on_period" => "boolean",
            "nhs_contact_type" => "nullable|in:GP,Epileptic Specialist,111,999",
            "postictal_state_end" => "nullable|date",
            "ambulance_called" => "boolean",
            "slept_after" => "boolean",
            "notes" => "nullable|string",
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
            "start_time.required" => "Please specify when the seizure started.",
            "start_time.date" =>
                "The start time must be a valid date and time.",
            "end_time.after" => "The end time must be after the start time.",
            "duration_minutes.integer" =>
                "Duration must be a whole number of minutes.",
            "duration_minutes.min" => "Duration cannot be negative.",
            "severity.required" => "Please rate the severity of the seizure.",
            "severity.integer" => "Severity must be a number.",
            "severity.min" => "Severity must be at least 1.",
            "severity.max" => "Severity cannot be greater than 10.",
            "nhs_contact_type.in" => "Please select a valid NHS contact type.",
            "postictal_state_end.date" =>
                "The postictal state end time must be a valid date and time.",
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
            "start_time" => "start time",
            "end_time" => "end time",
            "duration_minutes" => "duration",
            "nhs_contact_type" => "NHS contact type",
            "postictal_state_end" => "postictal state end time",
            "ambulance_called" => "ambulance called",
            "slept_after" => "slept after",
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure boolean fields are properly formatted
        $this->merge([
            "on_period" => $this->boolean("on_period"),
            "ambulance_called" => $this->boolean("ambulance_called"),
            "slept_after" => $this->boolean("slept_after"),
        ]);
    }
}
