<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VitalUpdateRequest extends FormRequest
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
            'type' => 'required|string|max:255',
            'value' => 'required|numeric',
            'recorded_at' => 'required|date',
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
            'type.required' => 'Please specify the type of vital sign.',
            'type.max' => 'Vital type cannot exceed 255 characters.',
            'value.required' => 'Please enter a value for this vital sign.',
            'value.numeric' => 'The vital value must be a number.',
            'recorded_at.required' => 'Please specify when this vital was recorded.',
            'recorded_at.date' => 'The recorded time must be a valid date and time.',
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
            'recorded_at' => 'recorded time',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Trim whitespace from type and notes
        $this->merge([
            'type' => trim($this->type ?? ''),
            'notes' => trim($this->notes ?? ''),
        ]);
    }
}
