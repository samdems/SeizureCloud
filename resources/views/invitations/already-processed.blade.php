<x-layouts.auth :title="__('Invitation Already Processed')">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="text-center mb-6">
                @if($invitation->status === 'accepted')
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-green-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h1 class="text-2xl font-bold text-gray-900">Invitation Already Accepted</h1>
                    <p class="text-sm text-gray-600 mt-2">
                        This invitation has already been accepted
                    </p>
                @elseif($invitation->status === 'cancelled')
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <h1 class="text-2xl font-bold text-gray-900">Invitation Cancelled</h1>
                    <p class="text-sm text-gray-600 mt-2">
                        This invitation has been cancelled
                    </p>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h1 class="text-2xl font-bold text-gray-900">Invitation No Longer Valid</h1>
                    <p class="text-sm text-gray-600 mt-2">
                        This invitation is no longer available
                    </p>
                @endif
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
                        <p class="text-sm text-gray-500 mt-1">
                            <strong>Invited:</strong> {{ $invitation->email }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Status Details -->
            @if($invitation->status === 'accepted')
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-400 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm text-green-800 font-medium mb-1">Invitation accepted successfully</p>
                            @if($invitation->accepted_at)
                                <p class="text-sm text-green-700">Accepted on: {{ $invitation->accepted_at->format('F j, Y \a\t g:i A') }}</p>
                                <p class="text-sm text-green-600 mt-1">
                                    ({{ $invitation->accepted_at->diffForHumans() }})
                                </p>
                            @endif
                            @if($invitation->acceptedUser)
                                <p class="text-sm text-green-700 mt-2">
                                    <strong>Accepted by:</strong> {{ $invitation->acceptedUser->name }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- What's Next -->
                <div class="border border-gray-200 rounded-lg p-4 mb-6">
                    <h4 class="font-medium text-gray-900 mb-2">What's next?</h4>
                    <p class="text-sm text-gray-600">
                        The trusted contact relationship has been established.
                        @if($invitation->acceptedUser)
                            {{ $invitation->acceptedUser->name }} now has trusted access to {{ $invitation->inviter->name }}'s account.
                        @endif
                    </p>
                </div>
            @elseif($invitation->status === 'cancelled')
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <div>
                            <p class="text-sm text-gray-800 font-medium mb-1">This invitation was cancelled</p>
                            <p class="text-sm text-gray-600">
                                The person who sent this invitation decided to cancel it before it could be accepted.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- What's Next for Cancelled -->
                <div class="border border-gray-200 rounded-lg p-4 mb-6">
                    <h4 class="font-medium text-gray-900 mb-2">Want to become a trusted contact?</h4>
                    <p class="text-sm text-gray-600 mb-3">
                        If you still want access to {{ $invitation->inviter->name }}'s account, you can contact them directly to request a new invitation.
                    </p>
                    <div class="text-sm text-gray-500">
                        <p><strong>Contact:</strong> {{ $invitation->inviter->email }}</p>
                    </div>
                </div>
            @endif

            @if($invitation->access_note)
                <div class="border border-gray-200 rounded-lg p-4 mb-6">
                    <h4 class="font-medium text-gray-900 mb-2">Original message:</h4>
                    <p class="text-sm text-gray-600 italic">"{{ $invitation->access_note }}"</p>
                </div>
            @endif

            <!-- Actions -->
            <div class="mt-6 space-y-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md text-center block focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Go to Dashboard
                    </a>

                    @if($invitation->status === 'accepted' && auth()->user()->id === $invitation->accepted_user_id)
                        <a href="{{ route('settings.trusted-contacts.index') }}" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md text-center block focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            View Trusted Contacts
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md text-center block focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Login
                    </a>
                @endauth

                <a href="{{ route('home') }}" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md text-center block focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Return to Home
                </a>
            </div>

            <!-- Help -->
            <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                <p class="text-xs text-gray-500">
                    @if($invitation->status === 'cancelled')
                        If you believe this was cancelled by mistake, contact {{ $invitation->inviter->name }} directly.
                    @else
                        If you have questions about trusted contacts, check your account settings or contact support.
                    @endif
                </p>
            </div>
        </div>
    </div>
</x-layouts.auth>
