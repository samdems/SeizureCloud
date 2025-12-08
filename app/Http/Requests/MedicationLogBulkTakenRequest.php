<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MedicationLogBulkTakenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'period' => 'required|in:morning,afternoon,evening,bedtime',
            'taken_at' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'period.required' => 'Time period is required.',
            'period.in' => 'Invalid time period selected.',
            'taken_at.required' => 'Time taken is required.',
            'taken_at.date' => 'Please enter a valid date and time.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }
}
