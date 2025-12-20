<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserInvitation;
use App\Models\TrustedContact;
use App\Notifications\TrustedContactInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class InvitationController extends Controller
{
    /**
     * Show the invitation acceptance page
     */
    public function show($token)
    {
        $invitation = UserInvitation::where("token", $token)->first();

        if (!$invitation) {
            abort(404, "Invitation not found.");
        }

        if ($invitation->status !== "pending") {
            return view("invitations.already-processed", compact("invitation"));
        }

        if ($invitation->isExpired()) {
            $invitation->markAsExpired();
            return view("invitations.expired", compact("invitation"));
        }

        // Check if user is already logged in
        if (Auth::check()) {
            $user = Auth::user();

            // Check if this is the same email as the invitation
            if ($user->email === $invitation->email) {
                return $this->acceptInvitation($invitation, $user);
            } else {
                // User is logged in but with different email
                return view(
                    "invitations.email-mismatch",
                    compact("invitation", "user"),
                );
            }
        }

        // Check if user already exists with this email
        $existingUser = User::where("email", $invitation->email)->first();

        if ($existingUser) {
            // User exists but not logged in - show login form
            return view(
                "invitations.login-required",
                compact("invitation", "existingUser"),
            );
        }

        // User doesn't exist - show registration form
        return view("invitations.register", compact("invitation"));
    }

    /**
     * Accept invitation for logged-in user
     */
    public function accept(Request $request, $token)
    {
        $invitation = UserInvitation::where("token", $token)->first();

        if (!$invitation) {
            abort(404, "Invitation not found.");
        }

        if (!$invitation->isValid()) {
            return redirect()
                ->route("invitation.show", $token)
                ->with("error", "This invitation is no longer valid.");
        }

        $user = Auth::user();

        if (!$user) {
            return redirect()
                ->route("invitation.show", $token)
                ->with("error", "Please log in to accept this invitation.");
        }

        if ($user->email !== $invitation->email) {
            return redirect()
                ->route("invitation.show", $token)
                ->with(
                    "error",
                    "This invitation is for a different email address.",
                );
        }

        return $this->acceptInvitation($invitation, $user);
    }

    /**
     * Handle invitation acceptance after registration
     */
    public function acceptAfterRegistration(Request $request, $token)
    {
        $invitation = UserInvitation::where("token", $token)->first();

        if (!$invitation || !$invitation->isValid()) {
            return redirect()
                ->route("login")
                ->with("error", "Invalid or expired invitation.");
        }

        $user = Auth::user();

        if (!$user || $user->email !== $invitation->email) {
            return redirect()
                ->route("login")
                ->with(
                    "error",
                    "Please log in with the invited email address.",
                );
        }

        return $this->acceptInvitation($invitation, $user);
    }

    /**
     * Process the invitation acceptance
     */
    private function acceptInvitation(UserInvitation $invitation, User $user)
    {
        try {
            DB::beginTransaction();

            // Mark invitation as accepted
            $invitation->markAsAccepted($user);

            // Create trusted contact relationship
            $trustedContact = $invitation->createTrustedContact();

            DB::commit();

            if ($trustedContact) {
                return redirect()
                    ->route("dashboard")
                    ->with(
                        "success",
                        "Welcome! You now have trusted access to {$invitation->inviter->name}'s account. " .
                            "You can switch between accounts using the trusted contacts menu.",
                    );
            } else {
                return redirect()
                    ->route("dashboard")
                    ->with(
                        "info",
                        "This trusted contact relationship already exists. " .
                            "You can access {$invitation->inviter->name}'s account from your trusted contacts.",
                    );
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route("dashboard")
                ->with(
                    "error",
                    "There was an error processing your invitation. Please try again or contact support.",
                );
        }
    }

    /**
     * Resend an invitation
     */
    public function resend(UserInvitation $invitation)
    {
        $this->authorize("resend", $invitation);

        if (!$invitation->canBeResent()) {
            return back()->with(
                "error",
                "This invitation cannot be resent at this time.",
            );
        }

        try {
            $invitation->resend();

            // Send the invitation email
            Notification::route("mail", $invitation->email)->notify(
                new TrustedContactInvitation($invitation, $invitation->inviter),
            );

            return back()->with(
                "success",
                "Invitation resent to {$invitation->email}. The new invitation will expire in 7 days.",
            );
        } catch (\Exception $e) {
            return back()->with(
                "error",
                "Failed to resend invitation. Please try again.",
            );
        }
    }

    /**
     * Cancel an invitation
     */
    public function cancel(UserInvitation $invitation)
    {
        $this->authorize("cancel", $invitation);

        if ($invitation->status !== "pending") {
            return back()->with(
                "error",
                "Only pending invitations can be cancelled.",
            );
        }

        $invitation->markAsCancelled();

        return back()->with(
            "success",
            "Invitation to {$invitation->email} has been cancelled.",
        );
    }

    /**
     * Show invitations management page
     */
    public function manage()
    {
        $user = Auth::user();

        $sentInvitations = $user
            ->sentInvitations()
            ->orderBy("created_at", "desc")
            ->get();

        $receivedInvitations = UserInvitation::where("email", $user->email)
            ->with("inviter")
            ->orderBy("created_at", "desc")
            ->get();

        return view(
            "invitations.manage",
            compact("sentInvitations", "receivedInvitations"),
        );
    }

    /**
     * Switch to trusted account from invitation acceptance
     */
    public function switchToAccount(User $user)
    {
        $currentUser = Auth::user();

        // Check if current user has trusted access to this account
        if (!$currentUser->hasTrustedAccessTo($user)) {
            abort(403, "You do not have trusted access to this account.");
        }

        // Store the original user ID in session
        session(["viewing_as_trusted_contact" => true]);
        session(["original_user_id" => $currentUser->id]);
        session(["trusted_user_id" => $user->id]);

        return redirect()
            ->route("dashboard")
            ->with("success", "Now viewing {$user->name}'s account.");
    }

    /**
     * Decline a received invitation
     */
    public function decline($token)
    {
        $invitation = UserInvitation::where("token", $token)->first();

        if (!$invitation) {
            abort(404, "Invitation not found.");
        }

        if ($invitation->status !== "pending") {
            return redirect()
                ->route("dashboard")
                ->with("info", "This invitation has already been processed.");
        }

        $invitation->markAsCancelled();

        return redirect()
            ->route("dashboard")
            ->with("success", "Invitation declined successfully.");
    }

    /**
     * Preview invitation email (for testing)
     */
    public function preview($token)
    {
        if (!app()->environment("local")) {
            abort(404);
        }

        $invitation = UserInvitation::where("token", $token)->first();

        if (!$invitation) {
            abort(404, "Invitation not found.");
        }

        $notification = new \App\Notifications\TrustedContactInvitation(
            $invitation,
            $invitation->inviter,
        );
        $mailMessage = $notification->toMail(
            new \Illuminate\Notifications\AnonymousNotifiable(),
        );

        return view(
            "invitations.preview",
            compact("mailMessage", "invitation"),
        );
    }
}
