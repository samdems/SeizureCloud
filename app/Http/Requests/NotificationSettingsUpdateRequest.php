<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotificationSettingsUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'notify_medication_taken' => 'boolean',
            'notify_seizure_added' => 'boolean',
            'notify_trusted_contacts_medication' => 'boolean',
            'notify_trusted_contacts_seizures' => 'boolean',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'notify_medication_taken' => 'medication taken notifications',
            'notify_seizure_added' => 'seizure added notifications',
            'notify_trusted_contacts_medication' => 'trusted contact medication notifications',
            'notify_trusted_contacts_seizures' => 'trusted contact seizure notifications',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert null values to false for checkboxes
        $this->merge([
            'notify_medication_taken' => $this->boolean('notify_medication_taken'),
            'notify_seizure_added' => $this->boolean('notify_seizure_added'),
            'notify_trusted_contacts_medication' => $this->boolean('notify_trusted_contacts_medication'),
            'notify_trusted_contacts_seizures' => $this->boolean('notify_trusted_contacts_seizures'),
        ]);
    }
}
