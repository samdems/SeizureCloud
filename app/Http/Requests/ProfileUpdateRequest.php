<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
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
            "name" => ["required", "string", "max:255"],
            "email" => [
                "required",
                "string",
                "lowercase",
                "email",
                "max:255",
                Rule::unique("users")->ignore(auth()->id()),
            ],
            "account_type" => ["required", "in:patient,carer,medical"],
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
            "name.required" => "Please enter your name.",
            "name.max" => "Name cannot exceed 255 characters.",
            "email.required" => "Please enter your email address.",
            "email.email" => "Please enter a valid email address.",
            "email.max" => "Email cannot exceed 255 characters.",
            "email.unique" => "This email is already taken.",
            "account_type.required" => "Please select your account type.",
            "account_type.in" => "Please select a valid account type.",
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
            "name" => "full name",
            "email" => "email address",
            "account_type" => "account type",
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Trim whitespace and ensure proper formatting
        $this->merge([
            "name" => trim($this->name ?? ""),
            "email" => strtolower(trim($this->email ?? "")),
            "account_type" => $this->account_type ?? "patient",
        ]);
    }
}
