<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VitalStoreRequest extends FormRequest
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
            "type" => "required|string|max:255",
            "value" => [
                "required",
                "string",
                "max:255",
                function ($attribute, $value, $fail) {
                    if ($this->input("type") === "Blood Pressure") {
                        if (strpos($value, "/") !== false) {
                            $parts = explode("/", $value);
                            if (
                                count($parts) !== 2 ||
                                !is_numeric(trim($parts[0])) ||
                                !is_numeric(trim($parts[1]))
                            ) {
                                $fail(
                                    'Blood pressure must be in format "120/80".',
                                );
                            }
                        } elseif (!is_numeric($value)) {
                            $fail(
                                'Blood pressure must be numeric or in format "120/80".',
                            );
                        }
                    } else {
                        if (!is_numeric($value)) {
                            $fail("The vital value must be a number.");
                        }
                    }
                },
            ],
            "recorded_at" => "required|date",
            "notes" => "nullable|string|max:1000",
            "low_threshold" => "nullable|numeric",
            "high_threshold" => "nullable|numeric",
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
            "type.required" => "Please specify the type of vital sign.",
            "type.max" => "Vital type cannot exceed 255 characters.",
            "value.required" => "Please enter a value for this vital sign.",
            "value.string" =>
                "The vital value must be valid (e.g., 72.5 or 120/80).",
            "recorded_at.required" =>
                "Please specify when this vital was recorded.",
            "recorded_at.date" =>
                "The recorded time must be a valid date and time.",
            "notes.max" => "Notes cannot exceed 1000 characters.",
            "low_threshold.numeric" => "The low threshold must be a number.",
            "high_threshold.numeric" => "The high threshold must be a number.",
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
            "recorded_at" => "recorded time",
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Trim whitespace from type, value, and notes
        $this->merge([
            "type" => trim($this->type ?? ""),
            "value" => trim($this->value ?? ""),
            "notes" => trim($this->notes ?? ""),
        ]);
    }
}
