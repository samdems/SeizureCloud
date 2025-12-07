<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component {
    public string $current_password = "";
    public string $password = "";
    public string $password_confirmation = "";

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                "current_password" => [
                    "required",
                    "string",
                    "current_password",
                ],
                "password" => [
                    "required",
                    "string",
                    Password::defaults(),
                    "confirmed",
                ],
            ]);
        } catch (ValidationException $e) {
            $this->reset(
                "current_password",
                "password",
                "password_confirmation",
            );
            throw $e;
        }

        Auth::user()->update([
            "password" => $validated["password"],
        ]);

        $this->reset("current_password", "password", "password_confirmation");
        $this->dispatch("password-updated");
    }
};
?>

<div class="space-y-6">
    <div class="alert alert-info">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span>Use a strong password with at least 8 characters including letters, numbers, and symbols.</span>
    </div>

    <form method="POST" wire:submit="updatePassword" class="space-y-6">
        <div class="form-control w-full">
            <label class="label">
                <span class="label-text font-semibold">{{ __('Current Password') }}</span>
            </label>
            <input
                type="password"
                wire:model="current_password"
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
                wire:model="password"
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
                wire:model="password_confirmation"
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

            <x-action-message on="password-updated">
                <div class="badge badge-success gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    {{ __('Saved') }}
                </div>
            </x-action-message>
        </div>
    </form>
</div>
