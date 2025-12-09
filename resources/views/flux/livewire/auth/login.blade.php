<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="form-control gap-6">
            @csrf

            <!-- Email Address -->
            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text">{{ __('Email address') }}</span>
                </label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="email@example.com"
                    class="input input-bordered w-full @error('email') input-error @enderror"
                    required
                    autofocus
                    autocomplete="email"
                />
                @error('email')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-control w-full">
                <div class="flex justify-between items-center">
                    <label class="label">
                        <span class="label-text">{{ __('Password') }}</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="link link-primary text-sm" wire:navigate>
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>
                <input
                    type="password"
                    name="password"
                    placeholder="{{ __('Password') }}"
                    class="input input-bordered w-full @error('password') input-error @enderror"
                    required
                    autocomplete="current-password"
                />
                @error('password')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="form-control">
                <label class="label cursor-pointer justify-start gap-2">
                    <input type="checkbox" name="remember" class="checkbox checkbox-primary" {{ old('remember') ? 'checked' : '' }} />
                    <span class="label-text">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="btn btn-primary w-full" data-test="login-button">
                    {{ __('Log in') }}
                </button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
                <span>{{ __('Don\'t have an account?') }}</span>
                <a href="{{ route('register') }}" class="link link-primary" wire:navigate>{{ __('Sign up') }}</a>
            </div>
        @endif

        <div class="text-center text-xs text-zinc-500 dark:text-zinc-500 mt-4 space-x-2">
            <a href="{{ route('legal.terms') }}" class="link link-primary" wire:navigate>Terms of Service</a>
            <span>•</span>
            <a href="{{ route('legal.privacy') }}" class="link link-primary" wire:navigate>Privacy Policy</a>
        </div>
    </div>
</x-layouts.auth>
