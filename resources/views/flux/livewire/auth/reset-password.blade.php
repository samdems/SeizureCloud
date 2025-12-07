<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Reset password')" :description="__('Please enter your new password below')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.update') }}" class="form-control gap-6">
            @csrf
            <!-- Token -->
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <!-- Email Address -->
            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text">{{ __('Email') }}</span>
                </label>
                <input
                    type="email"
                    name="email"
                    value="{{ request('email') }}"
                    class="input input-bordered w-full @error('email') input-error @enderror"
                    required
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
                <label class="label">
                    <span class="label-text">{{ __('Password') }}</span>
                </label>
                <input
                    type="password"
                    name="password"
                    placeholder="{{ __('Password') }}"
                    class="input input-bordered w-full @error('password') input-error @enderror"
                    required
                    autocomplete="new-password"
                />
                @error('password')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text">{{ __('Confirm password') }}</span>
                </label>
                <input
                    type="password"
                    name="password_confirmation"
                    placeholder="{{ __('Confirm password') }}"
                    class="input input-bordered w-full @error('password_confirmation') input-error @enderror"
                    required
                    autocomplete="new-password"
                />
                @error('password_confirmation')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="btn btn-primary w-full" data-test="reset-password-button">
                    {{ __('Reset password') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.auth>
