<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-base-200">

        <div class="drawer lg:drawer-open">
            <input id="sidebar-drawer" type="checkbox" class="drawer-toggle" />

            <!-- Main Content -->
            <div class="drawer-content flex flex-col">
                <!-- Mobile Navbar -->
                <div class="navbar bg-base-100 lg:hidden shadow-sm">
                    <div class="flex-none">
                        <label for="sidebar-drawer" class="btn btn-square btn-ghost drawer-button">
                            <x-heroicon-o-bars-3 class="inline-block w-5 h-5 stroke-current" />
                        </label>
                    </div>
                    <div class="flex-1"></div>
                    <div class="flex-none flex gap-2">
                        <a href="{{ route('dashboard') }}" wire:navigate class="btn btn-ghost btn-sm">
                            <x-heroicon-o-home class="w-5 h-5" />
                            Dashboard
                        </a>
                        <div class="dropdown dropdown-end">
                            <label tabindex="0" class="btn btn-ghost btn-circle avatar">
                                <x-avatar size="md" />
                            </label>
                            <ul tabindex="0" class="mt-3 z-[1] p-2 shadow-lg menu menu-sm dropdown-content bg-base-100 rounded-box w-64">
                                <li class="menu-title px-4 py-2">
                                    <div class="flex items-center gap-3">
                                        <x-avatar size="md" />
                                        <div class="flex flex-col">
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-base">{{ auth()->user()->name }}</span>
                                                @if(auth()->user()->isAdmin())
                                                    <x-admin-status-indicator :user="auth()->user()" size="xs" />
                                                @endif
                                            </div>
                                            <span class="text-xs opacity-70">{{ auth()->user()->email }}</span>
                                        </div>
                                    </div>
                                </li>

                                <div class="divider my-0"></div>
                                <li>
                                    <a href="{{ route('settings.profile') }}" wire:navigate>
                                        <x-heroicon-o-cog-6-tooth class="w-5 h-5" />
                                        Settings
                                    </a>
                                </li>
                                @if(auth()->user()->isAdmin())
                                <div class="divider my-0"></div>
                                <li>
                                    <a href="{{ route('admin.dashboard') }}" wire:navigate class="text-error">
                                        <x-heroicon-o-shield-check class="w-5 h-5" />
                                        Admin Panel
                                    </a>
                                </li>
                                @endif
                                <div class="divider my-0"></div>
                                <li>
                                    <a href="#" onclick="document.getElementById('sidebar-logout-form').submit(); return false;" class="text-error" data-test="logout-button">
                                        <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
                                        Log Out
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Page Content -->
                <main class="flex-1 p-4 lg:p-6">
                    {{ $slot }}
                </main>
            </div>

            <!-- Sidebar -->
            <div class="drawer-side z-40">
                <label for="sidebar-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
                <aside class="bg-base-100 min-h-full w-80 flex flex-col shadow-xl">
                    <!-- Logo Section -->
                    <div class="p-4 border-b border-base-300">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3" wire:navigate>
                            <x-app-logo />
                        </a>
                    </div>
                    <!-- Environment Warning Banner -->
                    @php
                        $env = config('app.env');
                    @endphp
                    @if($env === 'local')
                        <div class="w-full bg-warning/10 text-warning shadow-lg text-center py-4 my-2 font-bold z-50">
                            <div class="flex items-center justify-center gap-3">
                                <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                                You are viewing the LOCAL environment.
                            </div>
                        </div>
                    @elseif($env === 'staging')
                        <div class="w-full bg-error/10 text-error shadow-lg text-center py-4 my-2 font-bold z-50">
                            <div class="flex items-center justify-center gap-3">
                                <x-heroicon-o-wrench-screwdriver class="w-6 h-6" />
                                You are viewing the STAGING environment.
                            </div>
                        </div>
                    @endif
                    <!-- Trusted Access Indicator -->
                    @if(session('viewing_as_trusted_contact') && session('original_user_id'))
                        @php
                            $originalUser = \App\Models\User::find(session('original_user_id'));
                            $trustedUser = auth()->user();
                        @endphp
                        <div class="p-4 border-b border-info bg-info/10">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="badge badge-info">
                                    <x-heroicon-o-users class="w-3 h-3" />
                                    Trusted Access
                                </div>
                            </div>
                            <div class="text-sm">
                                <p class="font-medium text-base-content">Viewing {{ $trustedUser->name }}'s account</p>
                                <p class="text-xs text-base-content/70 mt-1">as {{ $originalUser->name }}</p>
                            </div>
                            <form action="{{ route('trusted-access.switch-back') }}" method="POST" class="mt-3">
                                @csrf
                                <button type="submit" class="btn btn-xs btn-outline btn-info w-full">
                                    <x-heroicon-o-arrow-uturn-left class="w-3 h-3" />
                                    Switch back to your account
                                </button>
                            </form>
                        </div>
                    @endif



                    <!-- Navigation Menu -->
                    <ul class="menu menu-lg px-4 py-6 flex-1 gap-2">
                        <li class="menu-title">
                            <span class="flex items-center gap-2">
                                <x-heroicon-o-home class="w-4 h-4" />
                                Main
                            </span>
                        </li>
                        <li>
                            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" wire:navigate>
                                <x-heroicon-o-home class="w-5 h-5" />
                                Dashboard
                            </a>
                        </li>



                        @if(auth()->user()->canTrackSeizures())
                        <li class="menu-title mt-4">
                            <span class="flex items-center gap-2">
                                <x-heroicon-o-signal class="w-4 h-4" />
                                Health Tracking
                            </span>
                        </li>
                        <li>
                            <a href="{{ route('seizures.index') }}" class="{{ request()->routeIs('seizures.*') ? 'active' : '' }}" wire:navigate>
                                <x-heroicon-o-chart-bar class="w-5 h-5" />
                                Seizure Tracker
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('vitals.index') }}" class="{{ request()->routeIs('vitals.*') ? 'active' : '' }}" wire:navigate>
                                <x-heroicon-o-heart class="w-5 h-5" />
                                Vitals Tracker
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('observations.index') }}" class="{{ request()->routeIs('observations.*') ? 'active' : '' }}" wire:navigate>
                                <x-heroicon-o-document-text class="w-5 h-5" />
                                Observations
                            </a>
                        </li>

                        <li class="menu-title mt-4">
                            <span class="flex items-center gap-2">
                                <x-heroicon-o-beaker class="w-4 h-4" />
                                Medications
                            </span>
                        </li>
                        <li>
                            <a href="{{ route('medications.schedule') }}" class="{{ request()->routeIs('medications.schedule*') ? 'active' : '' }}" wire:navigate>
                                <x-heroicon-o-calendar class="w-5 h-5" />
                                Today's Schedule
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('medications.index') }}" class="{{ request()->routeIs('medications.*') && !request()->routeIs('medications.schedule*') ? 'active' : '' }}" wire:navigate>
                                <x-heroicon-o-beaker class="w-5 h-5" />
                                Manage Medications
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('medications.schedule.history') }}" class="{{ request()->routeIs('medications.schedule.history') ? 'active' : '' }}" wire:navigate>
                                <x-heroicon-o-clock class="w-5 h-5" />
                                Weekly History
                            </a>
                        </li>
                        @endif


                        <li class="menu-title mt-4">
                            <span class="flex items-center gap-2">
                                <x-heroicon-o-folder class="w-4 h-4" />
                                Documents
                            </span>
                        </li>
                        <li>
                            <a href="{{ route('documents.index') }}" class="{{ request()->routeIs('documents.*') ? 'active' : '' }}" wire:navigate>
                                <x-heroicon-o-document-text class="w-5 h-5" />
                                My Documents
                            </a>
                        </li>
                        @if(auth()->user()->isAdmin())
                        <li class="menu-title mt-4">
                            <span class="flex items-center gap-2">
                                <x-heroicon-o-shield-check class="w-4 h-4" />
                                Administration
                            </span>
                        </li>
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}" wire:navigate>
                                <x-heroicon-o-cog-6-tooth class="w-5 h-5" />
                                Admin Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}" wire:navigate>
                                <x-heroicon-o-users class="w-5 h-5" />
                                User Management
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}" wire:navigate>
                                <x-heroicon-o-wrench-screwdriver class="w-5 h-5" />
                                System Settings
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.logs') }}" class="{{ request()->routeIs('admin.logs') ? 'active' : '' }}" wire:navigate>
                                <x-heroicon-o-document-text class="w-5 h-5" />
                                System Logs
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.email-logs') }}" class="{{ request()->routeIs('admin.email-logs') ? 'active' : '' }}" wire:navigate>
                                <x-heroicon-o-envelope class="w-5 h-5" />
                                Email Logs
                            </a>
                        </li>
                        @endif
                    </ul>

                    <!-- User Profile Section -->
                    <div class="p-4 border-t border-base-300 hidden lg:block">
                        <div class="dropdown dropdown-top dropdown-end w-full">
                            <label tabindex="0" class="btn btn-ghost w-full justify-start gap-3 h-auto py-3" data-test="sidebar-menu-button">
                                <x-avatar size="md" />
                                <div class="flex flex-col items-start flex-1 min-w-0">
                                    <div class="flex items-center gap-2 w-full">
                                        <span class="font-semibold text-sm truncate">{{ auth()->user()->name }}</span>
                                        @if(auth()->user()->isAdmin())
                                            <x-admin-status-indicator :user="auth()->user()" size="xs" />
                                        @endif
                                    </div>
                                    <span class="text-xs opacity-70 truncate w-full">{{ auth()->user()->email }}</span>
                                </div>
                                <x-heroicon-o-chevron-up-down class="w-5 h-5" />
                            </label>
                            <ul tabindex="0" class="dropdown-content menu p-2 shadow-lg bg-base-100 rounded-box w-full mb-2">
                                <li>
                                    <a href="{{ route('settings.profile') }}" wire:navigate>
                                        <x-heroicon-o-cog-6-tooth class="w-5 h-5" />
                                        Settings
                                    </a>
                                </li>
                                @if(auth()->user()->isAdmin())
                                <div class="divider my-0"></div>
                                <li>
                                    <a href="{{ route('admin.dashboard') }}" wire:navigate class="text-error">
                                        <x-heroicon-o-shield-check class="w-5 h-5" />
                                        Admin Panel
                                    </a>
                                </li>
                                @endif
                                <div class="divider my-0"></div>
                                <li>
                                    <a href="#" onclick="document.getElementById('sidebar-logout-form').submit(); return false;" class="text-error" data-test="logout-button">
                                        <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
                                        Log Out
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </aside>
            </div>
        </div>

        <!-- Hidden logout form for sidebar -->
        <form id="sidebar-logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
            @csrf
        </form>

        <!-- Floating Admin Button -->
        <x-floating-admin-button />

        @livewireScripts
    </body>
</html>
