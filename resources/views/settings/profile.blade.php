<x-layouts.app :title="__('Profile Settings')">
    <x-settings.layout>
        <div class="mb-6">
            <h2 class="text-2xl font-bold">Profile Information</h2>
            <p class="text-base-content/70 mt-2">Update your account profile information and email address</p>
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
            <form action="{{ route('settings.profile.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text font-semibold">{{ __('Name') }}</span>
                </label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $user->name) }}"
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
                    <span class="label-text font-semibold">{{ __('Account Type') }}</span>
                </label>
                <select
                    name="account_type"
                    class="select select-bordered w-full @error('account_type') select-error @enderror"
                    required
                >
                    @foreach(\App\Models\User::getAccountTypes() as $type => $description)
                        <option value="{{ $type }}" {{ old('account_type', $user->account_type) === $type ? 'selected' : '' }}>
                            {{ $description }}
                        </option>
                    @endforeach
                </select>
                @error('account_type')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
                <label class="label">
                    <span class="label-text-alt">This determines what features you can access</span>
                </label>
            </div>

            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text font-semibold">{{ __('Email') }}</span>
                </label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    class="input input-bordered w-full @error('email') input-error @enderror"
                    required
                    autocomplete="email"
                />
                @error('email')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                    <div class="alert alert-warning mt-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <div class="text-sm">
                                {{ __('Your email address is unverified.') }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="submit" class="btn btn-primary" data-test="update-profile-button">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    {{ __('Save Changes') }}
                </button>
            </div>
            </form>

            <div class="divider my-8"></div>

            <div class="card bg-base-200 border border-error/20">
                <div class="card-body">
                    <h3 class="card-title text-error">{{ __('Delete Account') }}</h3>
                    <p class="text-sm text-base-content/70 mb-4">
                        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
                    </p>
                    <div class="card-actions">
                        <button type="button" class="btn btn-error btn-outline" onclick="delete_account_modal.showModal()">
                            {{ __('Delete Account') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Delete Account Modal -->
            <dialog id="delete_account_modal" class="modal">
                <div class="modal-box">
                    <h3 class="font-bold text-lg text-error">{{ __('Delete Account') }}</h3>
                    <p class="py-4 text-sm">
                        {{ __('Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                    </p>
                    <form method="POST" action="{{ route('profile.destroy') }}">
                        @csrf
                        @method('DELETE')
                        <div class="form-control w-full mb-4">
                            <label class="label">
                                <span class="label-text font-semibold">{{ __('Password') }}</span>
                            </label>
                            <input
                                type="password"
                                name="password"
                                class="input input-bordered w-full"
                                placeholder="{{ __('Password') }}"
                                required
                            />
                        </div>
                        <div class="modal-action">
                            <button type="button" class="btn" onclick="delete_account_modal.close()">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-error">{{ __('Delete Account') }}</button>
                        </div>
                    </form>
                </div>
            </dialog>
        </div>
    </x-settings.layout>
</x-layouts.app>
