<x-layouts.auth :title="__('Invitation Expired')">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="text-center mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-red-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h1 class="text-2xl font-bold text-gray-900">Invitation Expired</h1>
                <p class="text-sm text-gray-600 mt-2">
                    This invitation is no longer valid
                </p>
            </div>

            <!-- Invitation Details -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                <div class="flex items-start space-x-3">
                    <x-avatar :user="$invitation->inviter" size="md" />
                    <div>
                        <h3 class="font-medium text-gray-900">{{ $invitation->inviter->name }}</h3>
                        <p class="text-sm text-gray-600">invited you to become a trusted contact</p>
                        @if($invitation->nickname)
                            <p class="text-sm text-gray-700 mt-1">
                                <strong>Role:</strong> {{ $invitation->nickname }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Expiration Notice -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-400 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="text-sm text-red-800 font-medium mb-1">This invitation expired on:</p>
                        <p class="text-sm text-red-700">{{ $invitation->invitation_expires_at->format('F j, Y \a\t g:i A') }}</p>
                        <p class="text-sm text-red-600 mt-2">
                            ({{ $invitation->invitation_expires_at->diffForHumans() }})
                        </p>
                    </div>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="space-y-4">
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-2">Want to accept this invitation?</h4>
                    <p class="text-sm text-gray-600 mb-3">
                        Contact <strong>{{ $invitation->inviter->name }}</strong> directly to request a new invitation.
                    </p>
                    <div class="text-sm text-gray-500">
                        <p><strong>Their email:</strong> {{ $invitation->inviter->email }}</p>
                    </div>
                </div>

                @if($invitation->access_note)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">Original message:</h4>
                        <p class="text-sm text-gray-600 italic">"{{ $invitation->access_note }}"</p>
                    </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="mt-6 space-y-3">
                <a href="{{ route('home') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md text-center block focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Return to Home
                </a>

                @auth
                    <a href="{{ route('dashboard') }}" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md text-center block focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md text-center block focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Login
                    </a>
                @endauth
            </div>

            <!-- Help -->
            <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                <p class="text-xs text-gray-500">
                    Invitations are valid for 7 days from when they're sent.
                    If you need access, ask {{ $invitation->inviter->name }} to send you a new invitation.
                </p>
            </div>
        </div>
    </div>
</x-layouts.auth>
