@props([
    'user' => null,
    'size' => 'md',
    'class' => '',
    'showInitialsFallback' => true,
])

@php
    // Size mappings
    $sizeClasses = [
        'xs' => 'w-6 h-6',
        'sm' => 'w-8 h-8',
        'md' => 'w-10 h-10',
        'lg' => 'w-12 h-12',
        'xl' => 'w-16 h-16',
        '2xl' => 'w-20 h-20',
    ];

    $textSizeClasses = [
        'xs' => 'text-xs',
        'sm' => 'text-xs',
        'md' => 'text-sm',
        'lg' => 'text-base',
        'xl' => 'text-lg',
        '2xl' => 'text-xl',
    ];

    $pixelSizes = [
        'xs' => 24,
        'sm' => 32,
        'md' => 40,
        'lg' => 48,
        'xl' => 64,
        '2xl' => 80,
    ];

    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    $textSizeClass = $textSizeClasses[$size] ?? $textSizeClasses['md'];
    $pixelSize = $pixelSizes[$size] ?? $pixelSizes['md'];

    $avatarUser = $user ?? auth()->user();
    $hasAvatarStyle = $avatarUser && method_exists($avatarUser, 'avatarUrl') && $avatarUser->avatar_style;
@endphp

<div class="avatar {{ $class }}">
    @if($hasAvatarStyle && $avatarUser->avatar_style !== 'initials')
        {{-- Use DiceBear API generated image avatar --}}
        <div class="{{ $sizeClass }} rounded-full overflow-hidden">
            <img
                src="{{ $avatarUser->avatarUrl($pixelSize) }}"
                alt="{{ $avatarUser->name ?? 'Avatar' }}"
                class="w-full h-full object-cover"
                loading="lazy"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
            />
            {{-- Fallback to initials if image fails to load --}}
            @if($showInitialsFallback)
                <div class="{{ $sizeClass }} rounded-full bg-primary text-primary-content items-center justify-center font-bold {{ $textSizeClass }}" style="display: none;">
                    {{ $avatarUser->initials() }}
                </div>
            @endif
        </div>
    @elseif($hasAvatarStyle && $avatarUser->avatar_style === 'initials')
        {{-- Use DiceBear initials style (still an image) --}}
        <div class="{{ $sizeClass }} rounded-full overflow-hidden">
            <img
                src="{{ $avatarUser->avatarUrl($pixelSize) }}"
                alt="{{ $avatarUser->name ?? 'Avatar' }}"
                class="w-full h-full object-cover"
                loading="lazy"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
            />
            {{-- Fallback to CSS initials if image fails to load --}}
            @if($showInitialsFallback)
                <div class="{{ $sizeClass }} rounded-full bg-primary text-primary-content items-center justify-center font-bold {{ $textSizeClass }}" style="display: none;">
                    {{ $avatarUser->initials() }}
                </div>
            @endif
        </div>
    @else
        {{-- Fallback to CSS-based initials avatar --}}
        <div class="{{ $sizeClass }} rounded-full bg-primary text-primary-content flex items-center justify-center font-bold {{ $textSizeClass }}">
            {{ $avatarUser ? $avatarUser->initials() : '?' }}
        </div>
    @endif
</div>
