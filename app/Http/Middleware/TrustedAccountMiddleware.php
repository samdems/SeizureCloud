<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrustedAccountMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip processing for logout routes to prevent conflicts
        if ($request->routeIs("logout")) {
            return $next($request);
        }

        // Check if user is viewing as a trusted contact
        if (
            session("viewing_as_trusted_contact") &&
            session("trusted_user_id")
        ) {
            $originalUser = Auth::user();
            $trustedUserId = session("trusted_user_id");

            // Add safety checks to prevent errors
            if (!$originalUser || !$trustedUserId) {
                // Clear invalid session data
                session()->forget([
                    "viewing_as_trusted_contact",
                    "original_user_id",
                    "trusted_user_id",
                ]);
                return $next($request);
            }

            $trustedUser = User::find($trustedUserId);
            if (!$trustedUser) {
                // Clear invalid session data if trusted user doesn't exist
                session()->forget([
                    "viewing_as_trusted_contact",
                    "original_user_id",
                    "trusted_user_id",
                ]);
                return $next($request);
            }

            // Verify the original user still has trusted access
            if (!$originalUser->hasTrustedAccessTo($trustedUser)) {
                // Revoke access if no longer valid
                session()->forget([
                    "viewing_as_trusted_contact",
                    "original_user_id",
                    "trusted_user_id",
                ]);

                return redirect()
                    ->route("dashboard")
                    ->with("error", "Your trusted access has been revoked.");
            }

            // Check for restricted routes when viewing as trusted contact
            $restrictedRoutes = [
                "settings.password",
                "settings.password.update",
                "settings.trusted-contacts.index",
                "settings.trusted-contacts.create",
                "settings.trusted-contacts.store",
                "settings.trusted-contacts.show",
                "settings.trusted-contacts.edit",
                "settings.trusted-contacts.update",
                "settings.trusted-contacts.destroy",
                "settings.trusted-contacts.toggle-status",
                "trusted-access.switch-to",
            ];

            if (
                $request->route() &&
                in_array($request->route()->getName(), $restrictedRoutes)
            ) {
                return redirect()
                    ->route("dashboard")
                    ->with(
                        "error",
                        "Access to this settings page is restricted when viewing another account.",
                    );
            }

            // Switch the authenticated user context for this request
            // Store original user for later reference
            $request->attributes->set("original_user", $originalUser);
            $request->attributes->set("viewing_as_trusted", true);

            // Replace the authenticated user for this request
            Auth::setUser($trustedUser);
        }

        return $next($request);
    }
}
