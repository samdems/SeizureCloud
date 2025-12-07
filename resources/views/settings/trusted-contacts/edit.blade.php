<x-layouts.app :title="__('Edit Trusted Contact')">
    <x-settings.layout>
        <div class="mb-6">
            <div class="flex items-center gap-4 mb-4">
                <a href="{{ route('settings.trusted-contacts.index') }}" class="btn btn-ghost btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Trusted Contacts
                </a>
            </div>
            <h2 class="text-2xl font-bold">Edit Trusted Contact</h2>
            <p class="text-base-content/70 mt-2">Update access settings for {{ $trustedContact->getDisplayName() }}</p>
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
            <!-- Contact Info -->
            <div class="card bg-base-200/50 border border-base-300/50 mb-6">
                <div class="card-body p-4">
                    <div class="flex items-center gap-3">
                        <x-avatar :user="$trustedContact->trustedUser" size="lg" />
                        <div>
                            <h3 class="font-semibold">{{ $trustedContact->trustedUser->name }}</h3>
                            <p class="text-sm text-base-content/70">{{ $trustedContact->trustedUser->email }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                @if($trustedContact->is_active)
                                    <span class="badge badge-success badge-sm">Active</span>
                                @else
                                    <span class="badge badge-error badge-sm">Inactive</span>
                                @endif
                                @if($trustedContact->isExpired())
                                    <span class="badge badge-warning badge-sm">Expired</span>
                                @endif
                                <span class="text-xs text-base-content/50">
                                    Access granted {{ $trustedContact->granted_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('settings.trusted-contacts.update', $trustedContact) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text font-semibold">{{ __('Nickname') }}</span>
                        <span class="label-text-alt">Optional</span>
                    </label>
                    <input
                        type="text"
                        name="nickname"
                        value="{{ old('nickname', $trustedContact->nickname) }}"
                        class="input input-bordered w-full @error('nickname') input-error @enderror"
                        placeholder="e.g., Mom, Caregiver, Emergency Contact"
                        maxlength="255"
                    />
                    @error('nickname')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @else
                        <label class="label">
                            <span class="label-text-alt">A friendly name to identify this trusted contact</span>
                        </label>
                    @enderror
                </div>

                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text font-semibold">{{ __('Access Note') }}</span>
                        <span class="label-text-alt">Optional</span>
                    </label>
                    <textarea
                        name="access_note"
                        class="textarea textarea-bordered h-24 @error('access_note') textarea-error @enderror"
                        placeholder="Describe why you're granting access to this person (e.g., Emergency contact, Primary caregiver, Medical guardian)"
                        maxlength="1000"
                    >{{ old('access_note', $trustedContact->access_note) }}</textarea>
                    @error('access_note')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @else
                        <label class="label">
                            <span class="label-text-alt">Optional note explaining the purpose of this access</span>
                        </label>
                    @enderror
                </div>

                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text font-semibold">{{ __('Expiration Date') }}</span>
                        <span class="label-text-alt">Optional</span>
                    </label>
                    <input
                        type="datetime-local"
                        name="expires_at"
                        value="{{ old('expires_at', $trustedContact->expires_at?->format('Y-m-d\TH:i')) }}"
                        class="input input-bordered w-full @error('expires_at') input-error @enderror"
                        min="{{ now()->format('Y-m-d\TH:i') }}"
                    />
                    @error('expires_at')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @else
                        <label class="label">
                            <span class="label-text-alt">Leave blank for permanent access until manually revoked</span>
                        </label>
                    @enderror
                </div>

                <div class="flex items-center gap-4 pt-4">
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        {{ __('Update Access') }}
                    </button>
                    <a href="{{ route('settings.trusted-contacts.index') }}" class="btn btn-ghost">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>

            <div class="divider my-8"></div>

            <!-- Quick Actions -->
            <div class="grid gap-4 md:grid-cols-2">
                <!-- Toggle Status -->
                <div class="card bg-base-200/50 border border-base-300/50">
                    <div class="card-body p-4">
                        <h4 class="font-semibold mb-2">Access Status</h4>
                        <p class="text-sm text-base-content/70 mb-3">
                            @if($trustedContact->is_active)
                                This contact currently has active access to your account.
                            @else
                                This contact's access is currently disabled.
                            @endif
                        </p>
                        <form action="{{ route('settings.trusted-contacts.toggle-status', $trustedContact) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $trustedContact->is_active ? 'btn-warning' : 'btn-success' }}">
                                @if($trustedContact->is_active)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    Deactivate Access
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Activate Access
                                @endif
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Remove Access -->
                <div class="card bg-base-200/50 border border-error/20">
                    <div class="card-body p-4">
                        <h4 class="font-semibold text-error mb-2">Remove Access</h4>
                        <p class="text-sm text-base-content/70 mb-3">
                            Permanently revoke this contact's access to your account.
                        </p>
                        <button type="button" class="btn btn-sm btn-error btn-outline" onclick="remove_access_modal.showModal()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Remove Access
                        </button>
                    </div>
                </div>
            </div>

            <!-- Remove Access Confirmation Modal -->
            <dialog id="remove_access_modal" class="modal">
                <div class="modal-box">
                    <h3 class="font-bold text-lg text-error">Remove Trusted Access</h3>
                    <p class="py-4 text-sm">
                        Are you sure you want to permanently revoke <strong>{{ $trustedContact->getDisplayName() }}</strong>'s access to your account?
                    </p>
                    <p class="text-sm text-base-content/70 mb-4">
                        This action cannot be undone. They will need to be re-added as a trusted contact to regain access.
                    </p>
                    <form action="{{ route('settings.trusted-contacts.destroy', $trustedContact) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-action">
                            <button type="button" class="btn" onclick="remove_access_modal.close()">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-error">{{ __('Remove Access') }}</button>
                        </div>
                    </form>
                </div>
            </dialog>
        </div>
    </x-settings.layout>
</x-layouts.app>
