<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmailVerificationController extends Controller
{
    public function verifyInvitedUser(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()
                ->route("login")
                ->with("error", "Please log in first.");
        }

        if (!($user->created_via_invitation ?? false)) {
            return redirect()
                ->route("verification.notice")
                ->with(
                    "error",
                    "Please complete the normal email verification process.",
                );
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()
                ->route("dashboard")
                ->with("info", "Your email was already verified.");
        }

        $user->markEmailAsVerified();

        Log::info("Manually verified email for invited user", [
            "user_id" => $user->id,
            "email" => $user->email,
            "ip" => $request->ip(),
            "user_agent" => $request->userAgent(),
        ]);

        return redirect()
            ->route("dashboard")
            ->with(
                "success",
                "Your email has been verified successfully. Welcome!",
            );
    }

    public function status(Request $request)
    {
        if (!config("app.debug")) {
            abort(404);
        }

        $user = Auth::user();

        if (!$user) {
            return response()->json(["error" => "No authenticated user"], 401);
        }

        return response()->json([
            "user_id" => $user->id,
            "email" => $user->email,
            "email_verified_at" => $user->email_verified_at,
            "has_verified_email" => $user->hasVerifiedEmail(),
            "created_via_invitation" => $user->created_via_invitation ?? false,
            "account_type" => $user->account_type,
        ]);
    }

    public function forceVerify(Request $request)
    {
        if (!config("app.debug")) {
            abort(404);
        }

        $user = Auth::user();

        if (!$user) {
            return response()->json(["error" => "No authenticated user"], 401);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            Log::info("Force verified email for user", [
                "user_id" => $user->id,
                "email" => $user->email,
                "created_via_invitation" =>
                    $user->created_via_invitation ?? false,
                "ip" => $request->ip(),
            ]);
        }

        return response()->json([
            "message" => "Email verification status updated",
            "user_id" => $user->id,
            "email" => $user->email,
            "email_verified_at" => $user->email_verified_at,
            "has_verified_email" => $user->hasVerifiedEmail(),
            "created_via_invitation" => $user->created_via_invitation ?? false,
        ]);
    }
}
