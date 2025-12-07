<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $appearance = "system";

    public function mount(): void
    {
        $this->appearance = session("appearance", "system");
    }

    public function updateAppearance(string $value): void
    {
        $this->appearance = $value;
        session(["appearance" => $value]);
        $this->dispatch("appearance-updated");
    }
};
?>

<x-layouts.app :title="__('Appearance Settings')">
    <x-settings.layout :heading="__('Appearance')" :subheading="__('Customize how the application looks on your device')">
        <div class="space-y-6">
            <div class="alert alert-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Choose your preferred theme. System will automatically match your device settings.</span>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text font-semibold text-lg">{{ __('Theme Preference') }}</span>
                </label>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Light Mode -->
                <div class="card bg-base-100 border-2 {{ $appearance === 'light' ? 'border-primary' : 'border-base-300' }} hover:border-primary transition-all cursor-pointer"
                     wire:click="updateAppearance('light')">
                    <div class="card-body items-center text-center p-6">
                        <div class="rounded-full bg-warning p-4 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                            </svg>
                        </div>
                        <h3 class="card-title text-lg">{{ __('Light') }}</h3>
                        <p class="text-sm opacity-70">{{ __('Bright and clear theme') }}</p>
                        @if($appearance === 'light')
                            <div class="badge badge-primary gap-2 mt-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                                Active
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Dark Mode -->
                <div class="card bg-base-100 border-2 {{ $appearance === 'dark' ? 'border-primary' : 'border-base-300' }} hover:border-primary transition-all cursor-pointer"
                     wire:click="updateAppearance('dark')">
                    <div class="card-body items-center text-center p-6">
                        <div class="rounded-full bg-primary p-4 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                            </svg>
                        </div>
                        <h3 class="card-title text-lg">{{ __('Dark') }}</h3>
                        <p class="text-sm opacity-70">{{ __('Easy on the eyes') }}</p>
                        @if($appearance === 'dark')
                            <div class="badge badge-primary gap-2 mt-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                                Active
                            </div>
                        @endif
                    </div>
                </div>

                <!-- System Mode -->
                <div class="card bg-base-100 border-2 {{ $appearance === 'system' ? 'border-primary' : 'border-base-300' }} hover:border-primary transition-all cursor-pointer"
                     wire:click="updateAppearance('system')">
                    <div class="card-body items-center text-center p-6">
                        <div class="rounded-full bg-info p-4 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                            </svg>
                        </div>
                        <h3 class="card-title text-lg">{{ __('System') }}</h3>
                        <p class="text-sm opacity-70">{{ __('Match device settings') }}</p>
                        @if($appearance === 'system')
                            <div class="badge badge-primary gap-2 mt-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                                Active
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </x-settings.layout>
</x-layouts.app>
