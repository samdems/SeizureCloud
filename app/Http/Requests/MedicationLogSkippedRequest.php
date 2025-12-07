<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicationLogSkippedRequest extends FormRequest
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
            'medication_id' => 'required|exists:medications,id',
            'medication_schedule_id' => 'nullable|exists:medication_schedules,id',
            'skip_reason' => 'nullable|string|max:255',
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
            'medication_id.required' => 'Please select a medication.',
            'medication_id.exists' => 'The selected medication is invalid.',
            'medication_schedule_id.exists' => 'The selected medication schedule is invalid.',
            'skip_reason.max' => 'Skip reason cannot exceed 255 characters.',
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
            'medication_id' => 'medication',
            'medication_schedule_id' => 'medication schedule',
            'skip_reason' => 'skip reason',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure medication_schedule_id is null if empty
        if ($this->medication_schedule_id === '') {
            $this->merge([
                'medication_schedule_id' => null,
            ]);
        }
    }
}
