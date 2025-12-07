<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrustedContactUpdateRequest extends FormRequest
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
            'nickname' => 'nullable|string|max:255',
            'access_note' => 'nullable|string|max:1000',
            'expires_at' => 'nullable|date|after:now',
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
            'nickname.max' => 'Nickname cannot exceed 255 characters.',
            'access_note.max' => 'Access note cannot exceed 1000 characters.',
            'expires_at.date' => 'Expiration date must be a valid date.',
            'expires_at.after' => 'Expiration date must be in the future.',
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
            'access_note' => 'access note',
            'expires_at' => 'expiration date',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Trim whitespace from text fields
        $this->merge([
            'nickname' => trim($this->nickname ?? ''),
            'access_note' => trim($this->access_note ?? ''),
        ]);

        // Set empty strings to null
        if (empty($this->nickname)) {
            $this->merge(['nickname' => null]);
        }

        if (empty($this->access_note)) {
            $this->merge(['access_note' => null]);
        }

        if (empty($this->expires_at)) {
            $this->merge(['expires_at' => null]);
        }
    }
}
