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
                                            <span class="font-semibold text-base">{{ auth()->user()->name }}</span>
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
                        <div class="w-full bg-warning/10 text-warning shadow-lg text-center py-2 font-bold z-50">
                            ⚠️ You are viewing the LOCAL environment.
                        </div>
                    @elseif($env === 'staging')
                        <div class="w-full bg-error/10 text-error shadow-lg text-center py-2 font-bold z-50">
                            🚧 You are viewing the STAGING environment.
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
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.611L5 14.5" />
                                </svg>
                                Manage Medications
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('medications.schedule.history') }}" class="{{ request()->routeIs('medications.schedule.history') ? 'active' : '' }}" wire:navigate>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                History
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
                                    <span class="font-semibold text-sm truncate w-full">{{ auth()->user()->name }}</span>
                                    <span class="text-xs opacity-70 truncate w-full">{{ auth()->user()->email }}</span>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                </svg>
                            </label>
                            <ul tabindex="0" class="dropdown-content menu p-2 shadow-lg bg-base-100 rounded-box w-full mb-2">
                                <li>
                                    <a href="{{ route('settings.profile') }}" wire:navigate>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Settings
                                    </a>
                                </li>
                                <div class="divider my-0"></div>
                                <li>
                                    <a href="#" onclick="document.getElementById('sidebar-logout-form').submit(); return false;" class="text-error" data-test="logout-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                                        </svg>
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

        @livewireScripts
    </body>
</html>
