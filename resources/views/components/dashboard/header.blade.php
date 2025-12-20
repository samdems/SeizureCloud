@props(['user' => null])

@php
    $user = $user ?? auth()->user();

    if (!$user) {
        return;
    }

    $isViewingAsTrustedContact = session('viewing_as_trusted_contact') && session('original_user_id');
    $originalUser = $isViewingAsTrustedContact ? \App\Models\User::find(session('original_user_id')) : null;
@endphp

<div class="flex items-center justify-between">
    <div>
        @if($isViewingAsTrustedContact)
            <h1 class="text-3xl font-bold text-base-content">
                Viewing {{ $user->name }}'s Account
                <span class="badge badge-primary badge-sm ml-2">{{ ucfirst($user->account_type) }}</span>
                @if($user->isAdmin())
                    <span class="badge badge-error badge-sm ml-2">Admin</span>
                @endif
            </h1>
        @else
            <h1 class="text-3xl font-bold text-base-content">
                Welcome back, {{ $user->name }}!
                <span class="badge badge-primary badge-sm ml-2">{{ ucfirst($user->account_type) }}</span>
                @if($user->isAdmin())
                    <span class="badge badge-error badge-sm ml-2">Admin</span>
                @endif
            </h1>
        @endif

        @if(session('viewing_as_trusted_contact'))
            <p class="text-base-content/70 mt-1">
                You are viewing this account as a trusted contact
                @if(!$user->canTrackSeizures())
                    • Limited access account
                @endif
            </p>
        @else
            <p class="text-base-content/70 mt-1">Here's your health overview for today</p>
        @endif
    </div>
    <div class="flex items-center gap-4">
        @if($user->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="btn btn-error btn-sm">
                <x-heroicon-o-shield-check class="w-4 h-4" />
                Admin Panel
            </a>
        @endif
        <div class="text-sm text-base-content/70">
            {{ now()->format('l, F j, Y') }}
        </div>
    </div>
</div>
