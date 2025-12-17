<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ObservationUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && $this->route('observation')->user_id === Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:10000',
            'observed_at' => 'required|date|before_or_equal:now',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please provide a title for your observation.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'description.required' => 'Please provide a description for your observation.',
            'description.max' => 'The description may not be greater than 10,000 characters.',
            'observed_at.required' => 'Please specify when this observation was made.',
            'observed_at.date' => 'Please provide a valid date and time.',
            'observed_at.before_or_equal' => 'The observation date cannot be in the future.',
        ];
    }

    /**
     * Get the validated data from the request.
     *
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated();
        $validated['user_id'] = Auth::id();

        return is_null($key) ? $validated : data_get($validated, $key, $default);
    }
}
