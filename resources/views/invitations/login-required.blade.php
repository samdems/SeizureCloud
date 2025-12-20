<x-layouts.auth :title="__('Login Required')">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Login Required</h1>
                <p class="text-sm text-gray-600 mt-2">
                    Please log in to accept this invitation
                </p>
            </div>

            <!-- Invitation Details -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start space-x-3">
                    <x-avatar :user="$invitation->inviter" size="md" />
                    <div>
                        <h3 class="font-medium text-gray-900">{{ $invitation->inviter->name }}</h3>
                        <p class="text-sm text-gray-600">has invited you to become a trusted contact</p>
                        @if($invitation->nickname)
                            <p class="text-sm text-blue-700 mt-1">
                                <strong>Your role:</strong> {{ $invitation->nickname }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Account Notice -->
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-400 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <p class="text-sm text-amber-800">
                            <strong>{{ $invitation->email }}</strong> already has an account.
                            Please log in to accept this invitation.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="hidden" name="intended" value="{{ route('invitation.show', $invitation->token) }}">

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
                    <input id="email" type="email" name="email" value="{{ $invitation->email }}" required autofocus
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700">{{ __('Password') }}</label>
                    <input id="password" type="password" name="password" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-500">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    {{ __('Login & Accept Invitation') }}
                </button>
            </form>

            <!-- Alternative Actions -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="text-center space-y-3">
                    <p class="text-xs text-gray-500">
                        Not {{ $existingUser->name }}?
                    </p>
                    <form action="{{ route('invitation.decline', $invitation->token) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 underline"
                                onclick="return confirm('Are you sure you want to decline this invitation?')">
                            Decline invitation
                        </button>
                    </form>
                </div>
            </div>

            <!-- Expiration notice -->
            <div class="mt-4 text-center">
                <p class="text-xs text-gray-500">
                    This invitation expires on {{ $invitation->invitation_expires_at->format('M j, Y \a\t g:i A') }}
                </p>
            </div>
        </div>
    </div>
</x-layouts.auth>
