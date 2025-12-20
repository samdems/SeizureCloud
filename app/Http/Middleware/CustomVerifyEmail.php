<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $redirectToRoute
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|null
     */
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        $user = $request->user();

        // If no user is authenticated, continue
        if (!$user) {
            return $next($request);
        }

        // If user doesn't need email verification, continue
        if (!$user instanceof MustVerifyEmail) {
            return $next($request);
        }

        // Check if user was created through invitation using safe column check
        $createdViaInvitation = false;
        try {
            // Try to access the column safely
            $createdViaInvitation = $user->created_via_invitation ?? false;
        } catch (\Exception $e) {
            // Column might not exist yet, use fallback method
            Log::info(
                "created_via_invitation column not available, using fallback method",
                [
                    "user_id" => $user->id,
                    "error" => $e->getMessage(),
                ],
            );
        }

        // If user was created through invitation (new method), auto-verify and continue
        if ($createdViaInvitation) {
            if (!$user->hasVerifiedEmail()) {
                Log::info(
                    "Auto-verifying email for invited user in middleware",
                    [
                        "user_id" => $user->id,
                        "email" => $user->email,
                        "route" => $request->route()->getName(),
                    ],
                );

                $user->markEmailAsVerified();
            }

            return $next($request);
        }

        // For users who were created through invitations (legacy/fallback check)
        // Check if they have accepted invitations
        if (method_exists($user, "wasCreatedThroughInvitation")) {
            try {
                if ($user->wasCreatedThroughInvitation()) {
                    if (!$user->hasVerifiedEmail()) {
                        Log::info(
                            "Auto-verifying email for legacy invited user in middleware",
                            [
                                "user_id" => $user->id,
                                "email" => $user->email,
                                "route" => $request->route()->getName(),
                            ],
                        );

                        $user->markEmailAsVerified();
                    }

                    return $next($request);
                }
            } catch (\Exception $e) {
                Log::warning("Error checking invitation status for user", [
                    "user_id" => $user->id,
                    "error" => $e->getMessage(),
                ]);
            }
        }

        // If email is already verified, continue
        if ($user->hasVerifiedEmail()) {
            return $next($request);
        }

        // If email is not verified and user wasn't invited, redirect to verification page
        Log::info("Redirecting user to email verification", [
            "user_id" => $user->id,
            "email" => $user->email,
            "route" => $request->route()->getName(),
            "created_via_invitation" => $user->created_via_invitation ?? false,
        ]);

        return $request->expectsJson()
            ? abort(403, "Your email address is not verified.")
            : Redirect::guest(
                URL::route($redirectToRoute ?: "verification.notice"),
            );
    }
}
