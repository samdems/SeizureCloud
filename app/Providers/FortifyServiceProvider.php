<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Contracts\RegisterResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();
        $this->configureLogoutResponse();
        $this->configureRegisterResponse();
    }

    /**
     * Configure Fortify actions.
     */
    private function configureActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::createUsersUsing(CreateNewUser::class);
    }

    /**
     * Configure Fortify views.
     */
    private function configureViews(): void
    {
        Fortify::loginView(fn() => view("flux.livewire.auth.login"));
        Fortify::verifyEmailView(
            fn() => view("flux.livewire.auth.verify-email"),
        );
        Fortify::twoFactorChallengeView(
            fn() => view("flux.livewire.auth.two-factor-challenge"),
        );
        Fortify::confirmPasswordView(
            fn() => view("flux.livewire.auth.confirm-password"),
        );
        Fortify::registerView(fn() => view("flux.livewire.auth.register"));
        Fortify::resetPasswordView(
            fn() => view("flux.livewire.auth.reset-password"),
        );
        Fortify::requestPasswordResetLinkView(
            fn() => view("flux.livewire.auth.forgot-password"),
        );
    }

    /**
     * Configure rate limiting.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for("two-factor", function (Request $request) {
            return Limit::perMinute(5)->by(
                $request->session()->get("login.id"),
            );
        });

        RateLimiter::for("login", function (Request $request) {
            $throttleKey = Str::transliterate(
                Str::lower($request->input(Fortify::username())) .
                    "|" .
                    $request->ip(),
            );

            return Limit::perMinute(5)->by($throttleKey);
        });
    }

    /**
     * Configure custom logout response.
     */
    private function configureLogoutResponse(): void
    {
        $this->app->instance(
            LogoutResponse::class,
            new class implements LogoutResponse {
                public function toResponse($request)
                {
                    return redirect("/");
                }
            },
        );
    }

    /**
     * Configure custom register response.
     */
    private function configureRegisterResponse(): void
    {
        $this->app->instance(
            RegisterResponse::class,
            new class implements RegisterResponse {
                public function toResponse($request)
                {
                    $user = auth()->user();

                    // Check if this was an invitation-based registration
                    if ($request->has("invitation_token")) {
                        $invitation = \App\Models\UserInvitation::where(
                            "token",
                            $request->input("invitation_token"),
                        )
                            ->where("email", $user->email)
                            ->where("status", "accepted")
                            ->first();

                        if ($invitation) {
                            // Ensure the user's email is verified and refresh the user model
                            $user->refresh();

                            // Log the verification status for debugging
                            Log::info("RegisterResponse for invited user", [
                                "user_id" => $user->id,
                                "email" => $user->email,
                                "email_verified_at" => $user->email_verified_at,
                                "has_verified_email" => $user->hasVerifiedEmail(),
                                "created_via_invitation" =>
                                    $user->created_via_invitation ?? false,
                            ]);

                            // If for some reason the email isn't verified, verify it now
                            if (!$user->hasVerifiedEmail()) {
                                $user->markEmailAsVerified();
                                Log::warning(
                                    "Had to manually verify email for invited user",
                                    [
                                        "user_id" => $user->id,
                                        "email" => $user->email,
                                    ],
                                );
                            }

                            // Switch to the inviter's account view if invitation was accepted
                            session(["viewing_as_trusted_contact" => true]);
                            session(["original_user_id" => $user->id]);
                            session([
                                "trusted_user_id" => $invitation->inviter_id,
                            ]);

                            return redirect()
                                ->route("dashboard")
                                ->with(
                                    "success",
                                    "Welcome! You now have trusted access to {$invitation->inviter->name}'s account. " .
                                        "You can switch between accounts using the account menu.",
                                );
                        }
                    }

                    // For non-invitation users, check if they need email verification
                    if (
                        $user &&
                        !$user->hasVerifiedEmail() &&
                        config("fortify.features") &&
                        in_array(
                            \Laravel\Fortify\Features::emailVerification(),
                            config("fortify.features"),
                        )
                    ) {
                        return redirect()->route("verification.notice");
                    }

                    // Default registration response for verified users
                    return redirect()->route("dashboard");
                }
            },
        );
    }
}
