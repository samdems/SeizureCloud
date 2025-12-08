@props(['accessibleAccounts' => null])

@php
    if (!auth()->user()) {
        return;
    }

    if (session('viewing_as_trusted_contact')) {
        return;
    }

    $accessibleAccounts = $accessibleAccounts ?? auth()->user()->validAccessibleAccounts()->take(5)->get();

    if ($accessibleAccounts->count() === 0) {
        return;
    }
@endphp

<div class="card bg-base-100 shadow-xl border-l-4 border-l-info">
    <div class="card-body">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-info/10 rounded-lg">
                    <x-heroicon-o-users class="w-6 h-6 text-info" />
                </div>
                <div>
                    <h2 class="card-title text-xl">Trusted Access</h2>
                    <p class="text-base-content/70 text-sm">Manage account access and switching</p>
                </div>
            </div>
            <a href="{{ route('settings.trusted-contacts.index') }}" class="btn btn-info btn-sm">
                <x-heroicon-o-cog-6-tooth class="w-4 h-4" />
                Manage
            </a>
        </div>

        <div>
            <h3 class="font-semibold mb-3">Accounts You Can Access</h3>
            <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
                @foreach($accessibleAccounts as $account)
                    <div class="card bg-base-200/50 border border-base-300/50">
                        <div class="card-body p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <x-avatar :user="$account" size="sm" />
                                    <div>
                                        <h4 class="font-medium text-sm">{{ $account->name }}</h4>
                                        @if($account->pivot->nickname)
                                            <p class="text-xs text-base-content/70">{{ $account->pivot->nickname }}</p>
                                        @endif
                                    </div>
                                </div>
                                @unless(session
('viewing_as_trusted_contact') && session('trusted_user_id') == $account->id)
                                    <form action="{{ route('trusted-access.switch-to', $account) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-primary">
                                            <x-heroicon-o-eye class="w-3 h-3" />
                                            View
                                        </button>
                                    </form>
                                @else
                                    <span class="badge badge-success badge-xs">Current</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if(auth()->user()->validAccessibleAccounts()->count() > 5)
                <div class="mt-3 text-center">
                    <a href="{{ route('settings.trusted-contacts.index') }}" class="link link-primary text-sm">
                        View all {{ auth()->user()->validAccessibleAccounts()->count() }} accessible accounts
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
