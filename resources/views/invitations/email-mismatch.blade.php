<x-layouts.auth :title="__('Email Mismatch')">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-base-200">
        <div class="w-full sm:max-w-md mt-6">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <!-- Header -->
                    <div class="text-center mb-6">
                        <h1 class="card-title text-2xl justify-center mb-2">Email Address Mismatch</h1>
                        <p class="text-sm text-base-content/70">
                            This invitation is for a different email address
                        </p>
                    </div>

                    <!-- Invitation Details -->
                    <div class="card bg-primary/10 border border-primary/20 mb-6">
                        <div class="card-body p-4">
                            <div class="flex items-start space-x-3">
                                <x-avatar :user="$invitation->inviter" size="md" />
                                <div>
                                    <h3 class="font-medium text-base-content">{{ $invitation->inviter->name }}</h3>
                                    <p class="text-sm text-base-content/70">has invited you to become a trusted contact</p>
                                    @if($invitation->nickname)
                                        <div class="badge badge-primary badge-sm mt-2">
                                            <strong>Your role:</strong> {{ $invitation->nickname }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mismatch Alert -->
                    <div class="alert alert-error mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <h4 class="font-bold">Email Address Mismatch</h4>
                            <div class="text-sm space-y-1">
                                <p><strong>Invitation for:</strong> {{ $invitation->email }}</p>
                                <p><strong>Currently logged in as:</strong> {{ $user->email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="space-y-4">
                        <!-- Option 1: Logout and access invitation -->
                        <div class="card bg-base-200 border border-base-300">
                            <div class="card-body p-4">
                                <h4 class="card-title text-base mb-2">Option 1: Access with invited email</h4>
                                <p class="text-sm text-base-content/70 mb-3">
                                    Log out and then log in with <strong>{{ $invitation->email }}</strong> to accept this invitation.
                                </p>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        Logout & Continue
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Option 2: Decline invitation -->
                        <div class="card bg-base-200 border border-base-300">
                            <div class="card-body p-4">
                                <h4 class="card-title text-base mb-2">Option 2: Decline invitation</h4>
                                <p class="text-sm text-base-content/70 mb-3">
                                    If this invitation was sent to you by mistake, you can decline it.
                                </p>
                                <form action="{{ route('invitation.decline', $invitation->token) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-error btn-sm"
                                            onclick="return confirm('Are you sure you want to decline this invitation?')">
                                        Decline Invitation
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Option 3: Return to dashboard -->
                        <div class="card bg-base-200 border border-base-300">
                            <div class="card-body p-4">
                                <h4 class="card-title text-base mb-2">Option 3: Return to your account</h4>
                                <p class="text-sm text-base-content/70 mb-3">
                                    Continue using your current account as <strong>{{ $user->name }}</strong>.
                                </p>
                                <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm">
                                    Go to Dashboard
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Support -->
                    <div class="divider mt-6"></div>
                    <div class="text-center">
                        <p class="text-xs text-base-content/60">
                            Need help? Contact {{ $invitation->inviter->name }} directly or reach out to support.
                        </p>
                    </div>

                    <!-- Expiration notice -->
                    <div class="text-center mt-4">
                        <div class="badge badge-outline badge-sm">
                            Expires {{ $invitation->invitation_expires_at->format('M j, Y \a\t g:i A') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.auth>
