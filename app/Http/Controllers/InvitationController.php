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

        if (Auth::check()) {
            $user = Auth::user();

            if ($user->email === $invitation->email) {
                return $this->acceptInvitation($invitation, $user);
            }

            return view(
                "invitations.email-mismatch",
                compact("invitation", "user"),
            );
        }

        $existingUser = User::where("email", $invitation->email)->first();

        if ($existingUser) {
            return view(
                "invitations.login-required",
                compact("invitation", "existingUser"),
            );
        }

        // User doesn't exist - show registration form
        return view("invitations.register", compact("invitation"));
    }

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

    private function acceptInvitation(UserInvitation $invitation, User $user)
    {
        try {
            DB::beginTransaction();

            $invitation->markAsAccepted($user);

            $trustedContact = $invitation->createTrustedContact();

            DB::commit();

            if (!$trustedContact) {
                return redirect()
                    ->route("dashboard")
                    ->with(
                        "info",
                        "This trusted contact relationship already exists. " .
                            "You can access {$invitation->inviter->name}'s account from your trusted contacts.",
                    );
            }

            return redirect()
                ->route("dashboard")
                ->with(
                    "success",
                    "Welcome! You now have trusted access to {$invitation->inviter->name}'s account. " .
                        "You can switch between accounts using the trusted contacts menu.",
                );
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

    public function switchToAccount(User $user)
    {
        $currentUser = Auth::user();

        if (!$currentUser->hasTrustedAccessTo($user)) {
            abort(403, "You do not have trusted access to this account.");
        }

        session(["viewing_as_trusted_contact" => true]);
        session(["original_user_id" => $currentUser->id]);
        session(["trusted_user_id" => $user->id]);

        return redirect()
            ->route("dashboard")
            ->with("success", "Now viewing {$user->name}'s account.");
    }

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
