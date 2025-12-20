<x-layouts.auth>
    @php
        $user = auth()->user();
        $isInvitedUser = $user && ($user->created_via_invitation ?? false);
        $isVerified = $user && $user->hasVerifiedEmail();
    @endphp

    @if($isInvitedUser)
        <script>
            console.log('Debug: Invited user detected, redirecting to dashboard');
            // Auto-redirect invited users who shouldn't see this page
            window.location.href = "{{ route('dashboard') }}";
        </script>
        <div class="mt-4 flex flex-col gap-6">
            <div class="alert alert-info">
                <p class="text-center">
                    {{ __('You were invited to this platform. Your email should already be verified.') }}
                </p>
                <p class="text-center text-sm">
                    {{ __('If you are not automatically redirected, click the button below.') }}
                </p>
            </div>

            <form method="POST" action="{{ route('verify-invited-user') }}" class="text-center">
                @csrf
                <button type="submit" class="btn btn-primary">
                    {{ __('Access Dashboard') }}
                </button>
            </form>
            @if(config('app.debug'))
                <div class="alert alert-warning text-xs">
                    <strong>Debug Info:</strong><br>
                    User ID: {{ $user->id }}<br>
                    Email: {{ $user->email }}<br>
                    Created via invitation: {{ $user->created_via_invitation ? 'Yes' : 'No' }}<br>
                    Email verified: {{ $isVerified ? 'Yes' : 'No' }}<br>
                    Verified at: {{ $user->email_verified_at }}
                </div>
            @endif
        </div>
    @else
        <div class="mt-4 flex flex-col gap-6">
            @if(config('app.debug') && $user)
                <div class="alert alert-warning text-xs">
                    <strong>Debug Info:</strong><br>
                    User ID: {{ $user->id }}<br>
                    Email: {{ $user->email }}<br>
                    Created via invitation: {{ $user->created_via_invitation ?? 'Unknown' }}<br>
                    Email verified: {{ $isVerified ? 'Yes' : 'No' }}<br>
                    Verified at: {{ $user->email_verified_at }}
                </div>
            @endif

            <p class="text-center">
                {{ __('Please verify your email address by clicking on the link we just emailed to you.') }}
            </p>

        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif

        <div class="flex flex-col items-center justify-between space-y-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-primary w-full">
                    {{ __('Resend verification email') }}
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-ghost text-sm" data-test="logout-button">
                    {{ __('Log out') }}
                </button>
            </form>
        </div>
    @endif
</x-layouts.auth>
