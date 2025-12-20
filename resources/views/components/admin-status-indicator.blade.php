@props(['user' => null, 'size' => 'sm', 'showText' => false])

@php
    $user = $user ?? auth()->user();

    if (!$user || !$user->isAdmin()) {
        return;
    }

    $sizeClasses = [
        'xs' => 'badge-xs',
        'sm' => 'badge-sm',
        'md' => '',
        'lg' => 'badge-lg'
    ];

    $badgeSize = $sizeClasses[$size] ?? $sizeClasses['sm'];
@endphp

<span class="badge badge-error {{ $badgeSize }} flex items-center gap-1">
    <x-heroicon-o-shield-check class="w-3 h-3" />
    @if($showText)
        Admin
    @endif
</span>
