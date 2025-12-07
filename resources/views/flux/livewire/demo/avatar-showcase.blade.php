<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public function getAvailableStyles(): array
    {
        return [
            'personas' => 'Personas - Modern illustrated avatars',
            'avataaars' => 'Avataaars - Sketch style avatars',
            'adventurer' => 'Adventurer - Adventure themed avatars',
            'big-ears' => 'Big Ears - Cute cartoon avatars',
            'big-smile' => 'Big Smile - Happy cartoon avatars',
            'bottts' => 'Bottts - Robot avatars',
            'croodles' => 'Croodles - Doodle style avatars',
            'initials' => 'Initials - Letter based avatars',
            'micah' => 'Micah - Simple illustrated faces',
            'miniavs' => 'Miniavs - Minimal avatars',
            'pixel-art' => 'Pixel Art - Retro pixel avatars',
        ];
    }

    public function getAvatarUrl(string $style, int $size = 80): string
    {
        return "https://api.dicebear.com/8.x/{$style}/svg?" .
            http_build_query([
                'seed' => auth()->user()->email,
                'size' => $size,
                'backgroundColor' => 'b6e3f4,c4b5fd,fbbf24,fb7185,34d399',
            ]);
    }
};
?>

<div class="space-y-6">
    <div class="card bg-base-100 border border-base-300">
        <div class="card-body">
            <h2 class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
                {{ __('Avatar Styles Showcase') }}
            </h2>
            <p class="text-base-content/70">
                {{ __('All available avatar styles generated from your email address.') }}
            </p>

            <div class="divider"></div>

            <!-- Avatar Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($this->getAvailableStyles() as $styleKey => $styleName)
                    <div class="card bg-base-100 border border-base-300 hover:shadow-lg transition-shadow">
                        <div class="card-body p-4 text-center">
                            <!-- Large Avatar -->
                            <div class="avatar mx-auto mb-4">
                                <div class="w-20 h-20 rounded-full">
                                    <img
                                        src="{{ $this->getAvatarUrl($styleKey, 80) }}"
                                        alt="{{ $styleName }}"
                                        class="w-full h-full rounded-full"
                                        loading="lazy"
                                    />
                                </div>
                            </div>

                            <!-- Style Info -->
                            <h3 class="font-bold text-base mb-1">{{ explode(' - ', $styleName)[0] }}</h3>
                            <p class="text-sm text-base-content/70 mb-4">{{ explode(' - ', $styleName)[1] ?? '' }}</p>

                            <!-- Size Variations -->
                            <div class="flex justify-center items-center gap-2 mb-3">
                                <span class="text-xs text-base-content/60">Sizes:</span>
                                <div class="avatar">
                                    <div class="w-4 h-4 rounded-full">
                                        <img src="{{ $this->getAvatarUrl($styleKey, 16) }}" alt="Small" class="w-full h-full rounded-full" loading="lazy" />
                                    </div>
                                </div>
                                <div class="avatar">
                                    <div class="w-6 h-6 rounded-full">
                                        <img src="{{ $this->getAvatarUrl($styleKey, 24) }}" alt="Medium" class="w-full h-full rounded-full" loading="lazy" />
                                    </div>
                                </div>
                                <div class="avatar">
                                    <div class="w-8 h-8 rounded-full">
                                        <img src="{{ $this->getAvatarUrl($styleKey, 32) }}" alt="Large" class="w-full h-full rounded-full" loading="lazy" />
                                    </div>
                                </div>
                            </div>

                            <!-- API URL -->
                            <div class="collapse collapse-arrow bg-base-200/50">
                                <input type="checkbox" />
                                <div class="collapse-title text-xs">
                                    View API URL
                                </div>
                                <div class="collapse-content">
                                    <code class="text-xs break-all">{{ $this->getAvatarUrl($styleKey, 80) }}</code>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="divider"></div>

            <!-- Usage Examples -->
            <div class="space-y-4">
                <h3 class="font-semibold text-lg">Usage Examples</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Component Usage -->
                    <div class="card bg-base-200/50 border border-base-300/50">
                        <div class="card-body p-4">
                            <h4 class="font-medium mb-3">Using Avatar Component</h4>
                            <div class="space-y-2">
                                <div class="flex items-center gap-3">
                                    <x-avatar size="xs" />
                                    <code class="text-sm">&lt;x-avatar size="xs" /&gt;</code>
                                </div>
                                <div class="flex items-center gap-3">
                                    <x-avatar size="sm" />
                                    <code class="text-sm">&lt;x-avatar size="sm" /&gt;</code>
                                </div>
                                <div class="flex items-center gap-3">
                                    <x-avatar size="md" />
                                    <code class="text-sm">&lt;x-avatar size="md" /&gt;</code>
                                </div>
                                <div class="flex items-center gap-3">
                                    <x-avatar size="lg" />
                                    <code class="text-sm">&lt;x-avatar size="lg" /&gt;</code>
                                </div>
                                <div class="flex items-center gap-3">
                                    <x-avatar size="xl" />
                                    <code class="text-sm">&lt;x-avatar size="xl" /&gt;</code>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Direct URL Usage -->
                    <div class="card bg-base-200/50 border border-base-300/50">
                        <div class="card-body p-4">
                            <h4 class="font-medium mb-3">Using avatarUrl() Method</h4>
                            <div class="space-y-2">
                                <div class="flex items-center gap-3">
                                    <div class="avatar">
                                        <div class="w-8 h-8 rounded-full">
                                            <img src="{{ auth()->user()->avatarUrl(32) }}" alt="Avatar" class="w-full h-full rounded-full" />
                                        </div>
                                    </div>
                                    <code class="text-sm">auth()->user()->avatarUrl(32)</code>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="avatar">
                                        <div class="w-10 h-10 rounded-full">
                                            <img src="{{ auth()->user()->avatarUrl(40) }}" alt="Avatar" class="w-full h-full rounded-full" />
                                        </div>
                                    </div>
                                    <code class="text-sm">auth()->user()->avatarUrl(40)</code>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="avatar">
                                        <div class="w-12 h-12 rounded-full">
                                            <img src="{{ auth()->user()->avatarUrl(48) }}" alt="Avatar" class="w-full h-full rounded-full" />
                                        </div>
                                    </div>
                                    <code class="text-sm">auth()->user()->avatarUrl(48)</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="alert alert-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
                <div>
                    <h4 class="font-medium">DiceBear API Integration</h4>
                    <p class="text-sm">All avatars are generated using the DiceBear API with your email as a unique seed. This ensures consistency across all avatar styles while providing beautiful, diverse representations.</p>
                </div>
            </div>
        </div>
    </div>
</div>
