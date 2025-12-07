<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public string $password = "";

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            "password" => ["required", "string", "current_password"],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect("/", navigate: true);
    }
};
?>

<section class="space-y-6">
    <div class="border-t border-base-300 pt-6">
        <div class="mb-4">
            <h3 class="text-lg font-bold text-error">{{ __('Delete Account') }}</h3>
            <p class="text-sm text-base-content/70 mt-1">{{ __('Permanently delete your account and all of its data') }}</p>
        </div>

        <button
            class="btn btn-error"
            onclick="confirm_delete_modal.showModal()"
            data-test="delete-user-button"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
            {{ __('Delete Account') }}
        </button>

        <dialog id="confirm_delete_modal" class="modal">
            <div class="modal-box">
                <h3 class="font-bold text-lg text-error mb-4">{{ __('Are you sure you want to delete your account?') }}</h3>

                <div class="alert alert-warning mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span class="text-sm">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. This action cannot be undone.') }}</span>
                </div>

                <form wire:submit="deleteUser" class="space-y-4">
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-semibold">{{ __('Confirm with your password') }}</span>
                        </label>
                        <input
                            type="password"
                            wire:model="password"
                            placeholder="{{ __('Enter your password') }}"
                            class="input input-bordered w-full @error('password') input-error @enderror"
                            autofocus
                        />
                        @error('password')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="modal-action">
                        <button type="button" class="btn" onclick="confirm_delete_modal.close()">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-error" data-test="confirm-delete-user-button">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                            {{ __('Delete Account') }}
                        </button>
                    </div>
                </form>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>
    </div>
</section>
