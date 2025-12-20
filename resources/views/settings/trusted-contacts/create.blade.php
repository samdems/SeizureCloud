<x-layouts.app :title="__('Add Trusted Contact')">
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
            <h2 class="text-2xl font-bold">Add Trusted Contact</h2>
            <p class="text-base-content/70 mt-2">Grant someone access to view your account information</p>
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
            <form action="{{ route('settings.trusted-contacts.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text font-semibold">{{ __('Email Address') }}</span>
                        <span class="label-text-alt text-error">*</span>
                    </label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="input input-bordered w-full @error('email') input-error @enderror"
                        placeholder="Enter the email address of the person you want to trust"
                        required
                        autofocus
                        autocomplete="email"
                    />
                    @error('email')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @else
                        <label class="label">
                            <span class="label-text-alt">If they don't have an account, we'll send them an invitation to join</span>
                        </label>
                    @enderror
                </div>

                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text font-semibold">{{ __('Nickname') }}</span>
                        <span class="label-text-alt">Optional</span>
                    </label>
                    <input
                        type="text"
                        name="nickname"
                        value="{{ old('nickname') }}"
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
                    >{{ old('access_note') }}</textarea>
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
                        value="{{ old('expires_at') }}"
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

                <div class="divider"></div>

                <div class="alert alert-info mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h3 class="font-bold">How It Works</h3>
                        <div class="text-sm mt-1">
                            <p class="mb-2">When you add a trusted contact:</p>
                            <ul class="list-disc list-inside space-y-1 ml-4">
                                <li><strong>If they have an account:</strong> Access is granted immediately</li>
                                <li><strong>If they don't have an account:</strong> They'll receive an email invitation to join and accept access</li>
                                <li><strong>Invitations expire in 7 days</strong> but can be resent if needed</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <h3 class="font-bold">Important Security Notice</h3>
                        <div class="text-sm mt-1">
                            <p class="mb-2">Trusted contacts will be able to:</p>
                            <ul class="list-disc list-inside space-y-1 ml-4">
                                <li>View all your seizure records and emergency status</li>
                                <li>See your medication schedules and logs</li>
                                <li>Access your vital signs data</li>
                                <li>View your account settings (but not change passwords)</li>
                            </ul>
                            <p class="mt-2 font-medium">Only grant access to people you completely trust with your medical information.</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4 pt-4">
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('Grant Access') }}
                    </button>
                    <a href="{{ route('settings.trusted-contacts.index') }}" class="btn btn-ghost">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>

        <script>
            // Client-side validation to prevent self-adding
            document.addEventListener('DOMContentLoaded', function() {
                const emailInput = document.querySelector('input[name="email"]');
                const currentUserEmail = '{{ auth()->user()->email }}';
                const form = document.querySelector('form');

                function validateEmail() {
                    const enteredEmail = emailInput.value.trim();
                    const submitButton = form.querySelector('button[type="submit"]');

                    if (enteredEmail === currentUserEmail) {
                        emailInput.classList.add('input-error');

                        // Remove existing error message
                        const existingError = form.querySelector('.self-email-error');
                        if (existingError) existingError.remove();

                        // Add error message
                        const errorMsg = document.createElement('label');
                        errorMsg.className = 'label self-email-error';
                        errorMsg.innerHTML = '<span class="label-text-alt text-error">You cannot add yourself as a trusted contact</span>';
                        emailInput.parentNode.appendChild(errorMsg);

                        submitButton.disabled = true;
                        submitButton.classList.add('btn-disabled');
                    } else {
                        emailInput.classList.remove('input-error');
                        const errorMsg = form.querySelector('.self-email-error');
                        if (errorMsg) errorMsg.remove();

                        submitButton.disabled = false;
                        submitButton.classList.remove('btn-disabled');
                    }
                }

                emailInput.addEventListener('input', validateEmail);
                emailInput.addEventListener('blur', validateEmail);
            });
        </script>
    </x-settings.layout>
</x-layouts.app>
