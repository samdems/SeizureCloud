<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TimePreferencesUpdateRequest extends FormRequest
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
            'morning_time' => 'required|date_format:H:i',
            'afternoon_time' => 'required|date_format:H:i',
            'evening_time' => 'required|date_format:H:i',
            'bedtime' => 'required|date_format:H:i',
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
            'morning_time.required' => 'Please specify your morning time.',
            'morning_time.date_format' => 'Morning time must be in HH:MM format.',
            'afternoon_time.required' => 'Please specify your afternoon time.',
            'afternoon_time.date_format' => 'Afternoon time must be in HH:MM format.',
            'evening_time.required' => 'Please specify your evening time.',
            'evening_time.date_format' => 'Evening time must be in HH:MM format.',
            'bedtime.required' => 'Please specify your bedtime.',
            'bedtime.date_format' => 'Bedtime must be in HH:MM format.',
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
            'morning_time' => 'morning time',
            'afternoon_time' => 'afternoon time',
            'evening_time' => 'evening time',
            'bedtime' => 'bedtime',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure all time values are properly trimmed
        $this->merge([
            'morning_time' => trim($this->morning_time ?? ''),
            'afternoon_time' => trim($this->afternoon_time ?? ''),
            'evening_time' => trim($this->evening_time ?? ''),
            'bedtime' => trim($this->bedtime ?? ''),
        ]);
    }
}
