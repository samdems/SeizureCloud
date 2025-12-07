<x-layouts.auth>
    <div class="flex flex-col gap-6 animate-fade-in">
        <x-auth-header :title="__('Create an account')" :description="__('Choose your account type and enter your details below')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="form-control gap-6">
            @csrf
            <!-- Name -->
            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text">{{ __('Name') }}</span>
                </label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="{{ __('Full name') }}"
                    class="input input-bordered w-full focus:input-primary transition-all duration-300 @error('name') input-error @enderror"
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
                    class="input input-bordered w-full focus:input-primary transition-all duration-300 @error('email') input-error @enderror"
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
                    class="input input-bordered w-full focus:input-primary transition-all duration-300 @error('password') input-error @enderror"
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
                    class="input input-bordered w-full focus:input-primary transition-all duration-300 @error('password_confirmation') input-error @enderror"
                    required
                    autocomplete="new-password"
                />
                @error('password_confirmation')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
            </div>

            <!-- Account Type Selection -->
            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text">{{ __('Account Type') }}</span>
                </label>
                <div class="bg-info/10 border border-info/20 rounded-lg p-4 mb-4">
                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-info mt-0.5 flex-shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                        <div class="text-sm">
                            <p class="font-medium text-content mb-1">Choose the right account type for you:</p>
                            <p class="text-content/80">
                                <strong>Patient accounts</strong> can track their own health data.
                                <strong>Caregiver and medical accounts</strong> access patient data through trusted relationships.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="space-y-3">
                    <!-- Patient Account -->
                    <label class="cursor-pointer block">
                        <input
                            type="radio"
                            name="account_type"
                            value="patient"
                            class="sr-only"
                            {{ old('account_type', 'patient') === 'patient' ? 'checked' : '' }}
                        />
                        <div class="card bg-base-200 border-2 border-transparent hover:border-primary transition-all duration-300 hover:shadow-lg hover:bg-primary/5">
                            <div class="card-body p-5">
                                <div class="flex items-start gap-4">
                                    <div class="p-3 bg-primary/10 rounded-lg flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-primary">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-lg text-primary">Patient Account</h3>
                                        <p class="text-sm text-base-content/70 mt-2 leading-relaxed">
                                            Track your own seizures, medications, and vitals. Full access to all health tracking features.
                                        </p>
                                        <div class="text-xs text-base-content/60 mt-3 space-y-1">
                                            <div class="flex items-center gap-2"><span class="text-success">✓</span> Seizure tracking & live timer</div>
                                            <div class="flex items-center gap-2"><span class="text-success">✓</span> Medication management</div>
                                            <div class="flex items-center gap-2"><span class="text-success">✓</span> Vitals monitoring</div>
                                            <div class="flex items-center gap-2"><span class="text-success">✓</span> Grant trusted access to caregivers</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </label>

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
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-accent">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
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
                                            <div class="flex items-center gap-2"><span class="text-warning">✗</span> Cannot track own health data</div>
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
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-success">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.611L5 14.5" />
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
                                            <div class="flex items-center gap-2"><span class="text-warning">✗</span> Cannot track own health data</div>
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
            </div>

            <div class="flex items-center justify-end mt-3">
                <button type="submit" class="btn btn-primary w-full btn-lg hover:opacity-95 transition-all duration-300 shadow-lg" data-test="register-user-button">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    {{ __('Create account') }}
                </button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <a href="{{ route('login') }}" class="link link-primary" wire:navigate>{{ __('Log in') }}</a>
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
