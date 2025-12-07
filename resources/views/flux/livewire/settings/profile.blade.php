<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = "";
    public string $email = "";

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            "name" => ["required", "string", "max:255"],
            "email" => [
                "required",
                "string",
                "lowercase",
                "email",
                "max:255",
                Rule::unique(User::class)->ignore($user->id),
            ],
        ]);

        $user->fill($validated);

        if ($user->isDirty("email")) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch("profile-updated", name: $user->name);
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(
                default: route("dashboard", absolute: false),
            );
            return;
        }

        $user->sendEmailVerificationNotification();
        Session::flash("status", "verification-link-sent");
    }
};
?>

<div class="space-y-6">
    <form wire:submit="updateProfileInformation" class="space-y-6">
        <div class="form-control w-full">
            <label class="label">
                <span class="label-text font-semibold">{{ __('Name') }}</span>
            </label>
            <input
                type="text"
                wire:model="name"
                class="input input-bordered w-full @error('name') input-error @enderror"
                required
                autofocus
                autocomplete="name"
            />
            @error('name')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
        </div>

        <div class="form-control w-full">
            <label class="label">
                <span class="label-text font-semibold">{{ __('Email') }}</span>
            </label>
            <input
                type="email"
                wire:model="email"
                class="input input-bordered w-full @error('email') input-error @enderror"
                required
                autocomplete="email"
            />
            @error('email')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&! auth()->user()->hasVerifiedEmail())
                <div class="alert alert-warning mt-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <div class="text-sm">
                            {{ __('Your email address is unverified.') }}
                            <button type="button" class="link link-primary" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </div>
                    </div>
                </div>

                @if (session('status') === 'verification-link-sent')
                    <div class="alert alert-success mt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ __('A new verification link has been sent to your email address.') }}</span>
                    </div>
                @endif
            @endif
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="btn btn-primary" data-test="update-profile-button">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                {{ __('Save Changes') }}
            </button>

            <x-action-message on="profile-updated">
                <div class="badge badge-success gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    {{ __('Saved') }}
                </div>
            </x-action-message>
        </div>
    </form>

    <div class="divider my-8"></div>

    <livewire:settings.delete-user-form />
</div>
