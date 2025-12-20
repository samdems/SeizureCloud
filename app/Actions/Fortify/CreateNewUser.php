<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $rules = [
            "name" => ["required", "string", "max:255"],
            "email" => [
                "required",
                "string",
                "email",
                "max:255",
                Rule::unique(User::class),
            ],
            "password" => $this->passwordRules(),
            "account_type" => ["required", "in:patient,carer,medical"],
        ];

        // Add invitation token validation if present
        if (isset($input["invitation_token"])) {
            $rules["invitation_token"] = ["required", "string"];
        }

        Validator::make($input, $rules)->validate();

        // Check if there's a valid invitation
        $invitation = null;
        if (isset($input["invitation_token"])) {
            $invitation = UserInvitation::where(
                "token",
                $input["invitation_token"],
            )
                ->where("email", $input["email"])
                ->where("status", "pending")
                ->first();

            if (!$invitation || !$invitation->isValid()) {
                throw ValidationException::withMessages([
                    "invitation_token" => ["Invalid or expired invitation."],
                ]);
            }
        }

        return DB::transaction(function () use ($input, $invitation) {
            $userData = [
                "name" => $input["name"],
                "email" => $input["email"],
                "password" => $input["password"],
                "account_type" => $input["account_type"],
            ];

            // Auto-verify email if user is registering through an invitation
            // This prevents invited users from receiving email verification emails
            // since the invitation process already validates the email address
            if ($invitation) {
                $userData["email_verified_at"] = now();
                $userData["created_via_invitation"] = true;

                Log::info("Auto-verifying email for invited user", [
                    "email" => $input["email"],
                    "invitation_id" => $invitation->id,
                    "invited_by" => $invitation->inviter->email ?? "unknown",
                ]);
            }

            $user = User::create($userData);

            // Process invitation if present - mark as accepted and create trusted contact
            if ($invitation) {
                $invitation->markAsAccepted($user);
                $invitation->createTrustedContact();

                Log::info(
                    "Successfully processed invitation during registration",
                    [
                        "user_id" => $user->id,
                        "email" => $user->email,
                        "invitation_id" => $invitation->id,
                        "email_verified" => $user->hasVerifiedEmail(),
                    ],
                );
            }

            return $user;
        });
    }
}
