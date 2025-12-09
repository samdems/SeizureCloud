<x-layouts.auth>
    <div class="flex flex-col gap-6 animate-fade-in max-w-6xl mx-auto">
        <x-auth-header :title="__('Create an account')" :description="__('Choose your account type and enter your details below')" />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="form-control gap-6">
            @csrf
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
                        wrapperClass=""
                    />

                    <x-form-field
                        name="email"
                        :label="__('Email address')"
                        type="email"
                        placeholder="email@example.com"
                        :value="old('email')"
                        required
                        autocomplete="email"
                        class="w-full focus:input-primary transition-all duration-300"
                        wrapperClass=""
                    />

                    <x-form-field
                        name="password"
                        :label="__('Password')"
                        type="password"
                        :placeholder="__('Password')"
                        required
                        autocomplete="new-password"
                        class="w-full focus:input-primary transition-all duration-300"
                        wrapperClass=""
                    />

                    <x-form-field
                        name="password_confirmation"
                        :label="__('Confirm password')"
                        type="password"
                        :placeholder="__('Confirm password')"
                        required
                        autocomplete="new-password"
                        class="w-full focus:input-primary transition-all duration-300"
                        wrapperClass=""
                    />
                </div>

                <!-- Account Type Section -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-base-content mb-4">{{ __('Account Type') }}</h3>
                    <x-account-type-selector />
                </div>
            </div>

            <div class="flex items-center justify-end mt-6">
                <button type="submit" class="btn btn-primary btn-lg hover:opacity-95 transition-all duration-300 shadow-lg" data-test="register-user-button">
                    <x-heroicon-o-user-plus class="h-5 w-5 mr-2" />
                    {{ __('Create account') }}
                </button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <a href="{{ route('login') }}" class="link link-primary" wire:navigate>{{ __('Log in') }}</a>
        </div>
    </div>


</x-layouts.auth>
