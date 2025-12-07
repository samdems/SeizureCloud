<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicationUpdateRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'dosage' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'prescriber' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'active' => 'boolean',
            'as_needed' => 'boolean',
            'notes' => 'nullable|string',
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
            'name.required' => 'Please enter the medication name.',
            'name.max' => 'Medication name cannot exceed 255 characters.',
            'dosage.max' => 'Dosage cannot exceed 255 characters.',
            'unit.max' => 'Unit cannot exceed 50 characters.',
            'prescriber.max' => 'Prescriber name cannot exceed 255 characters.',
            'start_date.date' => 'Start date must be a valid date.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after_or_equal' => 'End date must be on or after the start date.',
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
            'name' => 'medication name',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'as_needed' => 'as needed',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure boolean fields are properly formatted
        $this->merge([
            'active' => $this->boolean('active'),
            'as_needed' => $this->boolean('as_needed'),
        ]);
    }
}
