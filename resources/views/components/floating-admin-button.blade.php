@props(['user' => null])

@php
    $user = $user ?? auth()->user();

    if (!$user || !$user->isAdmin()) {
        return;
    }
@endphp

<!-- Floating Admin Button - Shows on all pages for admin users -->
<div class="fixed bottom-6 right-6 z-50">
    <div class="dropdown dropdown-top dropdown-end">
        <label tabindex="0" class="btn btn-error btn-circle shadow-lg hover:shadow-xl transition-all duration-300">
            <x-heroicon-o-shield-check class="w-6 h-6" />
        </label>
        <ul tabindex="0" class="dropdown-content menu p-3 shadow-2xl bg-base-100 rounded-box w-72 mb-2 border border-base-300">
            <li class="menu-title">
                <span class="flex items-center gap-2 text-error">
                    <x-heroicon-o-shield-check class="w-4 h-4" />
                    Admin Quick Actions
                </span>
            </li>

            <div class="divider my-1"></div>

            <li>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3" wire:navigate>
                    <x-heroicon-o-squares-2x2 class="w-4 h-4" />
                    <div>
                        <div class="font-medium">Admin Dashboard</div>
                        <div class="text-xs opacity-60">System overview</div>
                    </div>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3" wire:navigate>
                    <x-heroicon-o-users class="w-4 h-4" />
                    <div>
                        <div class="font-medium">User Management</div>
                        <div class="text-xs opacity-60">Manage accounts</div>
                    </div>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.settings') }}" class="flex items-center gap-3" wire:navigate>
                    <x-heroicon-o-cog-6-tooth class="w-4 h-4" />
                    <div>
                        <div class="font-medium">System Settings</div>
                        <div class="text-xs opacity-60">Configure system</div>
                    </div>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.logs') }}" class="flex items-center gap-3" wire:navigate>
                    <x-heroicon-o-document-text class="w-4 h-4" />
                    <div>
                        <div class="font-medium">System Logs</div>
                        <div class="text-xs opacity-60">View activity</div>
                    </div>
                </a>
            </li>

            <div class="divider my-1"></div>

            <li>
                <a href="{{ route('admin.export.users') }}" class="flex items-center gap-3">
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                    <div>
                        <div class="font-medium">Export Data</div>
                        <div class="text-xs opacity-60">Download user data</div>
                    </div>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Admin Status Toast - Shows briefly when user has admin access -->
<div class="toast toast-top toast-start" x-data="{ show: false }" x-init="show = true; setTimeout(() => show = false, 5000)" x-show="show" x-transition>
    <div class="alert alert-info">
        <x-heroicon-o-information-circle class="w-5 h-5" />
        <div>
            <div class="font-bold">Admin Access Active</div>
            <div class="text-sm">You have administrative privileges</div>
        </div>
    </div>
</div>
