<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppearanceUpdateRequest extends FormRequest
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
            'appearance' => 'required|in:light,dark,system',
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
            'appearance.required' => 'Please select an appearance preference.',
            'appearance.in' => 'Please select a valid appearance option (light, dark, or system).',
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
            'appearance' => 'appearance preference',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure appearance value is trimmed and lowercase
        $this->merge([
            'appearance' => strtolower(trim($this->appearance ?? '')),
        ]);
    }
}
