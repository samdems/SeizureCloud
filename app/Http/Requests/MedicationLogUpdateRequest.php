<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MedicationLogUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check that the medication log belongs to the authenticated user
        $medicationLog = $this->route("medicationLog");
        return $medicationLog &&
            $medicationLog->medication->user_id === Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            "taken_at" => ["required", "date"],
            "intended_time" => ["nullable", "date"],
            "dosage_taken" => ["nullable", "string", "max:255"],
            "notes" => ["nullable", "string", "max:1000"],
            "skipped" => ["nullable", "boolean"],
            "skip_reason" => [
                "nullable",
                "string",
                "max:255",
                "required_if:skipped,1",
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            "taken_at.required" => "The date and time is required.",
            "taken_at.date" => "Please provide a valid date and time.",
            "skip_reason.required_if" =>
                "Please provide a reason when marking as skipped.",
        ];
    }
}
