<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public string $avatarStyle = "initials";

    public function mount(): void
    {
        $user = Auth::user();
        // Check if avatar_style column exists before accessing it
        try {
            $this->avatarStyle = $user->avatar_style ?? "initials";
        } catch (\Exception $e) {
            $this->avatarStyle = "initials";
        }
    }

    public function updateAvatarStyle(): void
    {
        $this->validate([
            "avatarStyle" =>
                "required|string|in:personas,avataaars,adventurer,big-ears,big-smile,bottts,croodles,initials,micah,miniavs,pixel-art",
        ]);

        $user = Auth::user();

        // Check if avatar_style column exists before saving
        try {
            $user->avatar_style = $this->avatarStyle;
            $user->save();
            session()->flash("success", "Avatar style updated successfully!");
        } catch (\Exception $e) {
            session()->flash(
                "error",
                "Please run the migration first: php artisan migrate",
            );
        }
    }

    public function getAvatarUrl(string $style, int $size = 80): string
    {
        return "https://api.dicebear.com/8.x/{$style}/svg?" .
            http_build_query([
                "seed" => auth()->user()->email,
                "size" => $size,
                "backgroundColor" => "b6e3f4,c4b5fd,fbbf24,fb7185,34d399",
            ]);
    }
};
?>

<div class="space-y-6">
    @if (session()->has('success'))
        <div class="alert alert-success">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="mb-6">
        <h2 class="text-2xl font-bold">Avatar Settings</h2>
        <p class="text-base-content/70 mt-2">Choose your preferred avatar style. Your avatar is generated based on your email address.</p>
    </div>

    <!-- Current Avatar Preview -->
    <div class="flex items-center gap-4 mb-6">
        <div class="text-center">
            <div class="text-sm font-medium mb-2">Current Avatar</div>
            <div class="avatar">
                <div class="w-20 h-20 rounded-full">
                    <img src="{{ $this->getAvatarUrl($avatarStyle, 80) }}" alt="Current avatar" class="w-full h-full rounded-full" />
                </div>
            </div>
        </div>
        <div class="flex flex-col gap-1">
            <div class="text-sm">
                <span class="font-medium">Style:</span>
                {{ ucfirst(str_replace('-', ' ', $avatarStyle)) }}
            </div>
            <div class="text-sm">
                <span class="font-medium">Generated from:</span>
                {{ auth()->user()->email }}
            </div>
        </div>
    </div>

    <form wire:submit="updateAvatarStyle" class="space-y-6">
        <h3 class="font-semibold text-lg">Choose Avatar Style</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Personas -->
            <label class="cursor-pointer">
                <input type="radio" name="avatarStyle" value="personas" wire:model.live="avatarStyle" class="radio radio-primary sr-only" />
                <div class="card bg-base-100 border-2 transition-colors duration-200 hover:border-primary/50 {{ $avatarStyle === 'personas' ? 'border-primary bg-primary/5' : 'border-base-300' }}">
                    <div class="card-body p-4 text-center">
                        <div class="avatar mx-auto mb-3">
                            <div class="w-16 h-16 rounded-full">
                                <img src="{{ $this->getAvatarUrl('personas', 64) }}" alt="Personas" class="w-full h-full rounded-full" />
                            </div>
                        </div>
                        <h4 class="font-medium text-sm">Personas</h4>
                        <p class="text-xs text-base-content/70">Modern illustrated</p>
                    </div>
                </div>
            </label>

            <!-- Avataaars -->
            <label class="cursor-pointer">
                <input type="radio" name="avatarStyle" value="avataaars" wire:model.live="avatarStyle" class="radio radio-primary sr-only" />
                <div class="card bg-base-100 border-2 transition-colors duration-200 hover:border-primary/50 {{ $avatarStyle === 'avataaars' ? 'border-primary bg-primary/5' : 'border-base-300' }}">
                    <div class="card-body p-4 text-center">
                        <div class="avatar mx-auto mb-3">
                            <div class="w-16 h-16 rounded-full">
                                <img src="{{ $this->getAvatarUrl('avataaars', 64) }}" alt="Avataaars" class="w-full h-full rounded-full" />
                            </div>
                        </div>
                        <h4 class="font-medium text-sm">Avataaars</h4>
                        <p class="text-xs text-base-content/70">Sketch style</p>
                    </div>
                </div>
            </label>

            <!-- Initials -->
            <label class="cursor-pointer">
                <input type="radio" name="avatarStyle" value="initials" wire:model.live="avatarStyle" class="radio radio-primary sr-only" />
                <div class="card bg-base-100 border-2 transition-colors duration-200 hover:border-primary/50 {{ $avatarStyle === 'initials' ? 'border-primary bg-primary/5' : 'border-base-300' }}">
                    <div class="card-body p-4 text-center">
                        <div class="avatar mx-auto mb-3">
                            <div class="w-16 h-16 rounded-full">
                                <img src="{{ $this->getAvatarUrl('initials', 64) }}" alt="Initials" class="w-full h-full rounded-full" />
                            </div>
                        </div>
                        <h4 class="font-medium text-sm">Initials</h4>
                        <p class="text-xs text-base-content/70">Letter based</p>
                    </div>
                </div>
            </label>

            <!-- Adventurer -->
            <label class="cursor-pointer">
                <input type="radio" name="avatarStyle" value="adventurer" wire:model.live="avatarStyle" class="radio radio-primary sr-only" />
                <div class="card bg-base-100 border-2 transition-colors duration-200 hover:border-primary/50 {{ $avatarStyle === 'adventurer' ? 'border-primary bg-primary/5' : 'border-base-300' }}">
                    <div class="card-body p-4 text-center">
                        <div class="avatar mx-auto mb-3">
                            <div class="w-16 h-16 rounded-full">
                                <img src="{{ $this->getAvatarUrl('adventurer', 64) }}" alt="Adventurer" class="w-full h-full rounded-full" />
                            </div>
                        </div>
                        <h4 class="font-medium text-sm">Adventurer</h4>
                        <p class="text-xs text-base-content/70">Adventure themed</p>
                    </div>
                </div>
            </label>

            <!-- Bottts -->
            <label class="cursor-pointer">
                <input type="radio" name="avatarStyle" value="bottts" wire:model.live="avatarStyle" class="radio radio-primary sr-only" />
                <div class="card bg-base-100 border-2 transition-colors duration-200 hover:border-primary/50 {{ $avatarStyle === 'bottts' ? 'border-primary bg-primary/5' : 'border-base-300' }}">
                    <div class="card-body p-4 text-center">
                        <div class="avatar mx-auto mb-3">
                            <div class="w-16 h-16 rounded-full">
                                <img src="{{ $this->getAvatarUrl('bottts', 64) }}" alt="Bottts" class="w-full h-full rounded-full" />
                            </div>
                        </div>
                        <h4 class="font-medium text-sm">Bottts</h4>
                        <p class="text-xs text-base-content/70">Robot avatars</p>
                    </div>
                </div>
            </label>

            <!-- Pixel Art -->
            <label class="cursor-pointer">
                <input type="radio" name="avatarStyle" value="pixel-art" wire:model.live="avatarStyle" class="radio radio-primary sr-only" />
                <div class="card bg-base-100 border-2 transition-colors duration-200 hover:border-primary/50 {{ $avatarStyle === 'pixel-art' ? 'border-primary bg-primary/5' : 'border-base-300' }}">
                    <div class="card-body p-4 text-center">
                        <div class="avatar mx-auto mb-3">
                            <div class="w-16 h-16 rounded-full">
                                <img src="{{ $this->getAvatarUrl('pixel-art', 64) }}" alt="Pixel Art" class="w-full h-full rounded-full" />
                            </div>
                        </div>
                        <h4 class="font-medium text-sm">Pixel Art</h4>
                        <p class="text-xs text-base-content/70">Retro pixel</p>
                    </div>
                </div>
            </label>
        </div>

        <div class="divider"></div>

        <div class="flex justify-end">
            <button type="submit" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                Save Avatar Style
            </button>
        </div>
    </form>

    <div class="mt-6 p-4 bg-info/10 rounded-lg border border-info/20">
        <h4 class="font-medium text-info mb-2">About Avatar Generation</h4>
        <div class="text-sm text-base-content/80 space-y-1">
            <p>• Avatars are generated using your email address as a unique seed</p>
            <p>• Your avatar will be consistent across the application</p>
            <p>• Avatars are provided by DiceBear API and are free to use</p>
        </div>
    </div>
</div>
