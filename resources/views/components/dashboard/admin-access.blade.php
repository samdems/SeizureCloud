@props(['user' => null])

@php
    $user = $user ?? auth()->user();

    if (!$user || !$user->isAdmin()) {
        return;
    }

    // Get some quick admin stats
    $totalUsers = \App\Models\User::count();
    $adminUsers = \App\Models\User::where('is_admin', true)->count();
    $recentUsers = \App\Models\User::where('created_at', '>=', now()->subDays(7))->count();
@endphp

<!-- Admin Access Card - Admin Only -->
<div class="w-full">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold text-base-content flex items-center gap-2">
            <x-heroicon-o-shield-check class="w-6 h-6 text-error" />
            Administration
        </h2>
        <div class="badge badge-error badge-sm">Admin Access</div>
    </div>

    <div class="grid auto-rows-min gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <a href="{{ route('admin.dashboard') }}"
           class="card bg-error text-error-content hover:shadow-xl transition-all duration-300 hover:opacity-95">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="card-title text-xl mb-2">Admin Dashboard</h3>
                        <p class="opacity-80 text-sm">System overview & management</p>
                    </div>
                    <div class="text-3xl">🛡️</div>
                </div>
                <div class="mt-4 opacity-90">
                    <div class="text-2xl font-bold">{{ $totalUsers }}</div>
                    <div class="text-xs opacity-75">Total users</div>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.users.index') }}"
           class="card bg-warning text-warning-content hover:shadow-xl transition-all duration-300 hover:opacity-95">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="card-title text-xl mb-2">User Management</h3>
                        <p class="opacity-80 text-sm">Manage user accounts</p>
                    </div>
                    <div class="text-3xl">👥</div>
                </div>
                <div class="mt-4 opacity-90">
                    <div class="text-2xl font-bold">{{ $recentUsers }}</div>
                    <div class="text-xs opacity-75">New this week</div>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.settings') }}"
           class="card bg-info text-info-content hover:shadow-xl transition-all duration-300 hover:opacity-95">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="card-title text-xl mb-2">System Settings</h3>
                        <p class="opacity-80 text-sm">Configure system</p>
                    </div>
                    <div class="text-3xl">⚙️</div>
                </div>
                <div class="mt-4 opacity-90">
                    <div class="text-2xl font-bold">{{ $adminUsers }}</div>
                    <div class="text-xs opacity-75">Admin users</div>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.logs') }}"
           class="card bg-secondary text-secondary-content hover:shadow-xl transition-all duration-300 hover:opacity-95">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="card-title text-xl mb-2">System Logs</h3>
                        <p class="opacity-80 text-sm">Monitor activity</p>
                    </div>
                    <div class="text-3xl">📋</div>
                </div>
                <div class="mt-4 opacity-90">
                    <div class="text-2xl font-bold">{{ now()->format('H:i') }}</div>
                    <div class="text-xs opacity-75">Current time</div>
                </div>
            </div>
        </a>
    </div>
</div>
