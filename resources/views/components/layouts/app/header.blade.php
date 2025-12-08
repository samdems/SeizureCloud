<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark" data-theme="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <div class="navbar bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
            <div class="navbar-start">
                <div class="dropdown lg:hidden">
                    <label tabindex="0" class="btn btn-ghost btn-circle">
                        <x-heroicon-o-bars-3 class="inline-block w-5 h-5 stroke-current" />
                    </label>
                    <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                        <li><a href="{{ route('dashboard') }}" wire:navigate>{{ __('Dashboard') }}</a></li>

                    </ul>
                </div>
                <a href="{{ route('dashboard') }}" class="ms-2 flex items-center space-x-2" wire:navigate>
                    <x-app-logo />
                </a>
            </div>

            <div class="navbar-center hidden lg:flex">
                <ul class="menu menu-horizontal px-1">
                    <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" wire:navigate>
                        <x-heroicon-o-home class="w-5 h-5" />
                        {{ __('Dashboard') }}
                    </a></li>
                </ul>
            </div>

            <div class="navbar-end gap-2">




                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-ghost btn-circle avatar">
                        <x-avatar size="md" />
                    </label>
                    <ul tabindex="0" class="mt-3 z-[1] p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-52">
                        <li class="menu-title">
                            <div class="flex items-center gap-2 px-1 py-1.5">
                                <x-avatar size="sm" />
                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </li>
                        <li><a href="{{ route('settings.profile') }}" wire:navigate>
                            <x-heroicon-o-cog-6-tooth class="w-4 h-4" />
                            {{ __('Settings') }}
                        </a></li>
                        <li>
                            <a href="#" onclick="document.getElementById('logout-form').submit(); return false;" class="text-error" data-test="logout-button">
                                <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4" />
                                {{ __('Log Out') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Hidden logout form -->
        <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
            @csrf
        </form>

        <!-- Trusted Access Banner -->
        @if (session('viewing_as_trusted_contact') && session('original_user_id'))
            @php
                $originalUser = \App\Models\User::find(session('original_user_id'));
                $trustedUser = auth()->user();
            @endphp
            <div class="bg-info text-info-content border-b border-info/30">
                <div class="container mx-auto px-6 py-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-sm">
                            <x-heroicon-o-information-circle class="w-4 h-4" />
                            <span>You are viewing <strong>{{ $trustedUser->name }}'s</strong> account as a trusted contact</span>
                        </div>
                        <form action="{{ route('trusted-access.switch-back') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline btn-info">
                                Switch back to your account
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <main class="container mx-auto p-6">
            {{ $slot }}
        </main>

        @livewireScripts
    </body>
</html>
