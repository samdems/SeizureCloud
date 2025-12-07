<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LogoutResponse;

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
}
