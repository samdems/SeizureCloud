<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountTypeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(
        Request $request,
        Closure $next,
        ...$allowedTypes,
    ): Response {
        $user = $request->user();

        // If user is not authenticated, let auth middleware handle it
        if (!$user) {
            return $next($request);
        }

        // If no specific types are required, allow all authenticated users
        if (empty($allowedTypes)) {
            return $next($request);
        }

        // Get account type, defaulting to 'patient' if null
        $accountType = $user->account_type ?? "patient";

        // Check if user's account type is in the allowed types
        if (!in_array($accountType, $allowedTypes)) {
            // Determine appropriate redirect based on account type
            $redirectRoute = $this->getRedirectRoute($accountType);

            return redirect()
                ->route($redirectRoute)
                ->with(
                    "error",
                    "Your account type does not have permission to access this feature.",
                );
        }

        return $next($request);
    }

    /**
     * Get appropriate redirect route based on account type
     */
    private function getRedirectRoute(?string $accountType): string
    {
        return match ($accountType) {
            "patient" => "dashboard",
            "carer"
                => "dashboard", // Carers can still see dashboard with trusted access
            "medical" => "dashboard",
            null => "dashboard", // Handle null case
            default => "dashboard",
        };
    }
}
