<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicationScheduleStoreRequest extends FormRequest
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
            'scheduled_time' => 'required|date_format:H:i',
            'dosage_multiplier' => 'nullable|numeric|min:0.1|max:10',
            'frequency' => 'required|in:daily,weekly,as_needed',
            'days_of_week' => 'nullable|array',
            'days_of_week.*' => 'integer|min:0|max:6',
            'active' => 'boolean',
            'notes' => 'nullable|string|max:1000',
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
            'scheduled_time.required' => 'Please specify the scheduled time.',
            'scheduled_time.date_format' => 'The scheduled time must be in HH:MM format.',
            'dosage_multiplier.numeric' => 'Dosage multiplier must be a number.',
            'dosage_multiplier.min' => 'Dosage multiplier must be at least 0.1.',
            'dosage_multiplier.max' => 'Dosage multiplier cannot exceed 10.',
            'frequency.required' => 'Please select a frequency.',
            'frequency.in' => 'Please select a valid frequency option.',
            'days_of_week.array' => 'Days of week must be an array.',
            'days_of_week.*.integer' => 'Each day of week must be a number.',
            'days_of_week.*.min' => 'Day of week must be between 0 and 6.',
            'days_of_week.*.max' => 'Day of week must be between 0 and 6.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
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
            'scheduled_time' => 'scheduled time',
            'dosage_multiplier' => 'dosage multiplier',
            'days_of_week' => 'days of week',
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
        ]);

        // Set default dosage multiplier if not provided
        if (is_null($this->dosage_multiplier) || $this->dosage_multiplier === '') {
            $this->merge([
                'dosage_multiplier' => 1,
            ]);
        }

        // Ensure days_of_week is null if empty for non-weekly frequencies
        if ($this->frequency !== 'weekly' || empty($this->days_of_week)) {
            $this->merge([
                'days_of_week' => null,
            ]);
        }
    }
}
