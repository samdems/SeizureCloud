<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SeizureStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        // If no user_id specified, user is creating for themselves
        if (!$this->has("user_id")) {
            return true;
        }

        $targetUserId = $this->input("user_id");

        // If creating for themselves, always allowed
        if ($targetUserId == Auth::id()) {
            return true;
        }

        // Check if user has trusted access to the target user
        $targetUser = \App\Models\User::find($targetUserId);
        if (!$targetUser) {
            return false;
        }

        return Auth::user()->hasTrustedAccessTo($targetUser);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "user_id" => "nullable|integer|exists:users,id",
            "start_time" => "required|date",
            "end_time" => "nullable|date|after:start_time",
            "duration_minutes" => "nullable|integer|min:0",
            "severity" => "required|integer|min:1|max:10",

            // Seizure type
            "seizure_type" =>
                "nullable|in:focal_aware,focal_impaired,focal_motor,focal_non_motor,generalized_tonic_clonic,absence,myoclonic,atonic,tonic,clonic,unknown",

            // Video evidence
            "has_video_evidence" => "boolean",
            "video_notes" => "nullable|string|max:1000",

            // Triggers
            "triggers" => "nullable|array",
            "triggers.*" =>
                "string|in:stress,lack_of_sleep,missed_medication,illness,alcohol,flashing_lights,hormonal,dehydration,low_blood_sugar",
            "other_triggers" => "nullable|string|max:500",

            // Pre-ictal symptoms
            "pre_ictal_symptoms" => "nullable|array",
            "pre_ictal_symptoms.*" =>
                "string|in:aura,mood_change,headache,confusion,unusual_sensations,none_noticed",
            "pre_ictal_notes" => "nullable|string|max:1000",

            // Post-ictal recovery
            "recovery_time" =>
                "nullable|in:immediate,short,moderate,long,very_long",
            "post_ictal_confusion" => "boolean",
            "post_ictal_headache" => "boolean",
            "recovery_notes" => "nullable|string|max:1000",

            // Period and medical info
            "on_period" => "boolean",
            "days_since_period" => "nullable|integer|min:0|max:100",

            // Medication adherence
            "medication_adherence" => "nullable|in:excellent,good,fair,poor",
            "recent_medication_change" => "boolean",
            "experiencing_side_effects" => "boolean",
            "medication_notes" => "nullable|string|max:1000",

            // General wellbeing
            "wellbeing_rating" =>
                "nullable|in:excellent,good,fair,poor,very_poor",
            "sleep_quality" => "nullable|in:excellent,good,fair,poor,very_poor",
            "wellbeing_notes" => "nullable|string|max:1000",

            // NHS contact and emergency
            "nhs_contact_type" => "nullable|in:GP,Epileptic Specialist,111,999",
            "postictal_state_end" => "nullable|date",
            "ambulance_called" => "boolean",
            "slept_after" => "boolean",
            "notes" => "nullable|string",
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
            "user_id.integer" => "Invalid user selected.",
            "user_id.exists" => "The selected user does not exist.",
            "start_time.required" => "Please specify when the seizure started.",
            "start_time.date" =>
                "The start time must be a valid date and time.",
            "end_time.after" => "The end time must be after the start time.",
            "duration_minutes.integer" =>
                "Duration must be a whole number of minutes.",
            "duration_minutes.min" => "Duration cannot be negative.",
            "severity.required" => "Please rate the severity of the seizure.",
            "severity.integer" => "Severity must be a number.",
            "severity.min" => "Severity must be at least 1.",
            "severity.max" => "Severity cannot be greater than 10.",
            "nhs_contact_type.in" => "Please select a valid NHS contact type.",
            "postictal_state_end.date" =>
                "The postictal state end time must be a valid date and time.",
            "seizure_type.in" => "Please select a valid seizure type.",
            "triggers.*.in" => "One or more selected triggers are invalid.",
            "pre_ictal_symptoms.*.in" =>
                "One or more selected pre-ictal symptoms are invalid.",
            "recovery_time.in" => "Please select a valid recovery time.",
            "medication_adherence.in" =>
                "Please select a valid medication adherence level.",
            "wellbeing_rating.in" => "Please select a valid wellbeing rating.",
            "sleep_quality.in" => "Please select a valid sleep quality rating.",
            "days_since_period.max" =>
                "Days since period cannot be more than 100.",
            "video_notes.max" => "Video notes cannot exceed 1000 characters.",
            "other_triggers.max" =>
                "Other triggers description cannot exceed 500 characters.",
            "pre_ictal_notes.max" =>
                "Pre-ictal notes cannot exceed 1000 characters.",
            "recovery_notes.max" =>
                "Recovery notes cannot exceed 1000 characters.",
            "medication_notes.max" =>
                "Medication notes cannot exceed 1000 characters.",
            "wellbeing_notes.max" =>
                "Wellbeing notes cannot exceed 1000 characters.",
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
            "user_id" => "user",
            "start_time" => "start time",
            "end_time" => "end time",
            "duration_minutes" => "duration",
            "nhs_contact_type" => "NHS contact type",
            "postictal_state_end" => "postictal state end time",
            "ambulance_called" => "ambulance called",
            "slept_after" => "slept after",
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default user_id to current user if not specified
        if (!$this->has("user_id") || empty($this->input("user_id"))) {
            $this->merge(["user_id" => Auth::id()]);
        }

        // Ensure boolean fields are properly formatted
        $this->merge([
            "on_period" => $this->boolean("on_period"),
            "ambulance_called" => $this->boolean("ambulance_called"),
            "slept_after" => $this->boolean("slept_after"),
            "has_video_evidence" => $this->boolean("has_video_evidence"),
            "post_ictal_confusion" => $this->boolean("post_ictal_confusion"),
            "post_ictal_headache" => $this->boolean("post_ictal_headache"),
            "recent_medication_change" => $this->boolean(
                "recent_medication_change",
            ),
            "experiencing_side_effects" => $this->boolean(
                "experiencing_side_effects",
            ),
        ]);
    }
}
