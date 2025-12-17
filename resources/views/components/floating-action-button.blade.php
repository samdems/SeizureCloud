@props([
    'href' => null,
    'icon' => 'heroicon-o-plus',
    'text' => null,
    'position' => 'bottom-right', // bottom-right, bottom-left, bottom-center
    'color' => 'primary',
    'size' => 'normal', // small, normal, large
])

@php
    $positionClasses = match($position) {
        'bottom-right' => 'bottom-6 right-6',
        'bottom-left' => 'bottom-6 left-6',
        'bottom-center' => 'bottom-6 left-1/2 transform -translate-x-1/2',
        default => 'bottom-6 right-6'
    };

    $sizeClasses = match($size) {
        'small' => 'w-12 h-12',
        'normal' => 'w-16 h-16',
        'large' => 'w-20 h-20',
        default => 'w-16 h-16'
    };

    $iconSizeClasses = match($size) {
        'small' => 'w-5 h-5',
        'normal' => 'w-6 h-6',
        'large' => 'w-8 h-8',
        default => 'w-6 h-6'
    };

    $colorClasses = match($color) {
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'accent' => 'btn-accent',
        'success' => 'btn-success',
        'warning' => 'btn-warning',
        'error' => 'btn-error',
        'info' => 'btn-info',
        default => 'btn-primary'
    };
@endphp

@if($href)
    <a
        href="{{ $href }}"
        class="fixed z-50 btn btn-circle {{ $colorClasses }} {{ $sizeClasses }} {{ $positionClasses }} shadow-2xl hover:shadow-3xl transform hover:scale-110 transition-all duration-200 group"
        wire:navigate
        @if($text) title="{{ $text }}" @endif
    >
        @php
            $iconComponent = "x-{$icon}";
        @endphp
        <{!! $iconComponent !!} class="{{ $iconSizeClasses }} group-hover:rotate-90 transition-transform duration-200" />

        @if($text)
            <span class="absolute right-full mr-3 px-2 py-1 bg-base-100 text-base-content text-sm rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap border border-base-300">
                {{ $text }}
                <div class="absolute top-1/2 left-full transform -translate-y-1/2 w-0 h-0 border-l-4 border-l-base-100 border-t-4 border-t-transparent border-b-4 border-b-transparent"></div>
            </span>
        @endif
    </a>
@else
    <button
        {{ $attributes->class([
            'fixed z-50 btn btn-circle',
            $colorClasses,
            $sizeClasses,
            $positionClasses,
            'shadow-2xl hover:shadow-3xl transform hover:scale-110 transition-all duration-200 group'
        ]) }}
        @if($text) title="{{ $text }}" @endif
    >
        @php
            $iconComponent = "x-{$icon}";
        @endphp
        <{!! $iconComponent !!} class="{{ $iconSizeClasses }} group-hover:rotate-90 transition-transform duration-200" />

        @if($text)
            <span class="absolute right-full mr-3 px-2 py-1 bg-base-100 text-base-content text-sm rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap border border-base-300">
                {{ $text }}
                <div class="absolute top-1/2 left-full transform -translate-y-1/2 w-0 h-0 border-l-4 border-l-base-100 border-t-4 border-t-transparent border-b-4 border-b-transparent"></div>
            </span>
        @endif
    </button>
@endif
