<x-layouts.app :title="__('Password Settings')">
    <x-settings.layout>
        <div class="mb-6">
            <h2 class="text-2xl font-bold">Update Password</h2>
            <p class="text-base-content/70 mt-2">Ensure your account is using a long, random password to stay secure</p>
        </div>
        @if (session('success'))
            <div class="alert alert-success mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="max-w-2xl">
            <form method="POST" action="{{ route('settings.password.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="alert alert-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Use a strong password with at least 8 characters including letters, numbers, and symbols.</span>
            </div>

            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text font-semibold">{{ __('Current Password') }}</span>
                </label>
                <input
                    type="password"
                    name="current_password"
                    class="input input-bordered w-full @error('current_password') input-error @enderror"
                    required
                    autocomplete="current-password"
                />
                @error('current_password')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
            </div>

            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text font-semibold">{{ __('New Password') }}</span>
                </label>
                <input
                    type="password"
                    name="password"
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

            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text font-semibold">{{ __('Confirm New Password') }}</span>
                </label>
                <input
                    type="password"
                    name="password_confirmation"
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

            <div class="flex items-center gap-4 pt-4">
                <button type="submit" class="btn btn-primary" data-test="update-password-button">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    {{ __('Update Password') }}
                </button>
            </div>
            </form>
        </div>
    </x-settings.layout>
</x-layouts.app>
