<x-layouts.auth :title="__('Accept Invitation')">
    <div class="flex flex-col gap-6 animate-fade-in max-w-6xl mx-auto">
        <x-auth-header
            :title="__('You\'re Invited!')"
            :description="__('Complete your registration to accept this trusted contact invitation')"
        />

        <!-- Invitation Details Card -->
        <div class="card bg-base-200 border border-base-300">
            <div class="card-body">
                <div class="flex items-start gap-4">
                    <x-avatar :user="$invitation->inviter" size="lg" />
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg">{{ $invitation->inviter->name }}</h3>
                        <p class="text-base-content/70">has invited you to become a trusted contact</p>
                        @if($invitation->nickname)
                            <div class="badge badge-primary badge-outline mt-2">{{ $invitation->nickname }}</div>
                        @endif
                        @if($invitation->access_note)
                            <div class="mt-3 p-3 bg-base-100 rounded-lg border border-base-300">
                                <p class="text-sm">"{{ $invitation->access_note }}"</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- What This Means Alert -->
        <div class="alert alert-info">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h3 class="font-bold">As a trusted contact, you'll be able to:</h3>
                <div class="text-sm mt-2 space-y-1">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>View seizure records and emergency status</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Monitor medication schedules and logs</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Access vital signs and health data</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Registration Form -->
        <form method="POST" action="{{ route('register') }}" class="form-control gap-6">
            @csrf
            <input type="hidden" name="invitation_token" value="{{ $invitation->token }}">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- User Details Section -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-base-content mb-4">{{ __('Your Details') }}</h3>

                    <x-form-field
                        name="name"
                        :label="__('Name')"
                        type="text"
                        :placeholder="__('Full name')"
                        :value="old('name')"
                        required
                        autofocus
                        autocomplete="name"
                        class="w-full focus:input-primary transition-all duration-300"
                    />

                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-semibold">{{ __('Email address') }}</span>
                        </label>
                        <input
                            type="email"
                            name="email"
                            value="{{ $invitation->email }}"
                            readonly
                            class="input input-bordered w-full bg-base-200 cursor-not-allowed"
                        />
                        <label class="label">
                            <span class="label-text-alt">This email address is pre-filled from your invitation</span>
                        </label>
                    </div>

                    <x-form-field
                        name="password"
                        :label="__('Password')"
                        type="password"
                        :placeholder="__('Password')"
                        required
                        autocomplete="new-password"
                        class="w-full focus:input-primary transition-all duration-300"
                    />

                    <x-form-field
                        name="password_confirmation"
                        :label="__('Confirm password')"
                        type="password"
                        :placeholder="__('Confirm password')"
                        required
                        autocomplete="new-password"
                        class="w-full focus:input-primary transition-all duration-300"
                    />
                </div>

                <!-- Account Type Section -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-base-content mb-4">{{ __('Account Type') }}</h3>
                    <div class="form-control w-full">
                        <div class="space-y-3">
                            <!-- Caregiver Account -->
                            <label class="cursor-pointer block">
                                <input
                                    type="radio"
                                    name="account_type"
                                    value="carer"
                                    class="sr-only"
                                    {{ old('account_type') === 'carer' ? 'checked' : '' }}
                                />
                                <div class="card bg-base-200 border-2 border-transparent hover:border-primary transition-all duration-300 hover:shadow-lg hover:bg-accent/5">
                                    <div class="card-body p-5">
                                        <div class="flex items-start gap-4">
                                            <div class="p-3 bg-accent/10 rounded-lg flex-shrink-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-lg text-accent">Caregiver Account</h3>
                                                <p class="text-sm text-base-content/70 mt-2 leading-relaxed">
                                                    For family members or caregivers who need access to patient records through trusted relationships.
                                                </p>
                                                <div class="text-xs text-base-content/60 mt-3 space-y-1">
                                                    <div class="flex items-center gap-2"><span class="text-success">✓</span> View trusted patients' data</div>
                                                    <div class="flex items-center gap-2"><span class="text-success">✓</span> Assist with medication tracking</div>
                                                    <div class="flex items-center gap-2"><span class="text-success">✓</span> Emergency access features</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Medical Professional Account -->
                            <label class="cursor-pointer block">
                                <input
                                    type="radio"
                                    name="account_type"
                                    value="medical"
                                    class="sr-only"
                                    {{ old('account_type') === 'medical' ? 'checked' : '' }}
                                />
                                <div class="card bg-base-200 border-2 border-transparent hover:border-primary transition-all duration-300 hover:shadow-lg hover:bg-success/5">
                                    <div class="card-body p-5">
                                        <div class="flex items-start gap-4">
                                            <div class="p-3 bg-success/10 rounded-lg flex-shrink-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-lg text-success">Medical Professional</h3>
                                                <p class="text-sm text-base-content/70 mt-2 leading-relaxed">
                                                    For healthcare professionals who need access to patient records for medical care.
                                                </p>
                                                <div class="text-xs text-base-content/60 mt-3 space-y-1">
                                                    <div class="flex items-center gap-2"><span class="text-success">✓</span> Professional patient access</div>
                                                    <div class="flex items-center gap-2"><span class="text-success">✓</span> Clinical data review</div>
                                                    <div class="flex items-center gap-2"><span class="text-success">✓</span> Healthcare team coordination</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @error('account_type')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                        <label class="label">
                            <span class="label-text-alt">Choose the account type that best describes your role as a trusted contact</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Terms acceptance -->
            <div class="form-control">
                <label class="label cursor-pointer justify-start gap-3">
                    <input type="checkbox" name="terms" required class="checkbox checkbox-primary" />
                    <span class="label-text">
                        I agree to the
                        <a href="{{ route('legal.terms') }}" target="_blank" class="link link-primary">Terms of Service</a>
                        and
                        <a href="{{ route('legal.privacy') }}" target="_blank" class="link link-primary">Privacy Policy</a>
                    </span>
                </label>
                @error('terms')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-4 mt-6">
                <button type="submit" class="btn btn-primary btn-lg hover:opacity-95 transition-all duration-300 shadow-lg flex-1 sm:flex-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ __('Accept Invitation & Create Account') }}
                </button>

                <form action="{{ route('invitation.decline', $invitation->token) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-outline" onclick="return confirm('Are you sure you want to decline this invitation?')">
                        {{ __('Decline') }}
                    </button>
                </form>
            </div>
        </form>

        <!-- Expiration Notice -->
        <div class="text-center">
            <div class="text-xs text-base-content/50">
                This invitation expires on {{ $invitation->invitation_expires_at->format('M j, Y \a\t g:i A') }}
            </div>
        </div>

        <div class="text-center text-xs text-zinc-500 dark:text-zinc-500 mt-4 space-x-2">
            <a href="{{ route('legal.terms') }}" class="link link-primary" target="_blank">Terms of Service</a>
            <span>•</span>
            <a href="{{ route('legal.privacy') }}" class="link link-primary" target="_blank">Privacy Policy</a>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Account type selection interaction
        const accountTypeCards = document.querySelectorAll('input[name="account_type"]');
        const cards = document.querySelectorAll('input[name="account_type"] + div.card');

        function updateCardStyles() {
            accountTypeCards.forEach((radio, index) => {
                const card = cards[index];
                if (radio.checked) {
                    card.classList.add('border-primary', 'bg-primary/10', 'shadow-xl', 'ring-2', 'ring-primary/20');
                    card.classList.remove('border-transparent', 'bg-base-200');
                    // Add selected state styling
                    card.style.opacity = '0.95';
                } else {
                    card.classList.remove('border-primary', 'bg-primary/10', 'shadow-xl', 'ring-2', 'ring-primary/20');
                    card.classList.add('border-transparent', 'bg-base-200');
                    card.style.opacity = '1';
                }
            });
        }

        // Initial state
        updateCardStyles();

        // Update on change
        accountTypeCards.forEach(radio => {
            radio.addEventListener('change', updateCardStyles);
        });

        // Make entire card clickable
        cards.forEach((card, index) => {
            card.addEventListener('click', function(e) {
                // Prevent double triggering since label already handles the click
                if (e.target.type !== 'radio') {
                    accountTypeCards[index].checked = true;
                    updateCardStyles();
                }
            });
        });
    });
    </script>
</x-layouts.auth>
