@props([
    'items' => [],
    'position' => 'dropdown-end',
    'size' => 'btn-sm',
    'iconClass' => 'w-4 h-4',
    'menuClass' => 'menu-sm dropdown-content bg-base-100 rounded-xl w-56 border border-base-300 shadow-lg',
    'triggerClass' => 'btn btn-ghost btn-circle hover:bg-base-200 transition-colors duration-200',
])

@php
    // Merge default trigger classes with size
    $triggerClasses = trim($triggerClass . ' ' . $size);

    // Generate unique ID for accessibility
    $menuId = 'kebab-menu-' . uniqid();
@endphp

<div class="dropdown {{ $position }}">
    <label
        for="{{ $menuId }}"
        tabindex="0"
        class="{{ $triggerClasses }} focus:ring-2 focus:ring-primary focus:ring-opacity-50"
        aria-label="Menu options"
        role="button"
    >
        <x-heroicon-o-ellipsis-vertical class="{{ $iconClass }} text-base-content/70 hover:text-base-content transition-colors duration-200" />
    </label>

    <input type="checkbox" id="{{ $menuId }}" class="dropdown-toggle hidden" />

    <ul
        tabindex="0"
        class="mt-3 z-[1] p-3 shadow-xl menu {{ $menuClass }} backdrop-blur-sm"
        role="menu"
        aria-labelledby="{{ $menuId }}"
    >
        @forelse($items as $item)
            @if(isset($item['divider']) && $item['divider'])
                <li class="divider my-2 border-base-300"></li>
            @elseif(isset($item['header']))
                <li class="menu-title px-3 py-2">
                    <span class="text-xs font-bold text-base-content/60 uppercase tracking-wider">{{ $item['header'] }}</span>
                </li>
            @else
                <li>
                    @if(isset($item['href']))
                        <a
                            href="{{ $item['href'] }}"
                            @if(isset($item['wire:navigate']) && $item['wire:navigate']) wire:navigate @endif
                            @if(isset($item['onclick'])) onclick="{{ $item['onclick'] }}" @endif
                            @if(isset($item['target'])) target="{{ $item['target'] }}" @endif
                            class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg hover:bg-base-200 transition-all duration-200 group"
                            role="menuitem"
                        >
                            @if(isset($item['icon']))
                                <x-dynamic-component :component="$item['icon']" class="w-4 h-4 text-base-content/60 group-hover:text-base-content transition-colors duration-200" />
                            @endif
                            <span class="font-medium">{{ $item['label'] }}</span>
                        </a>
                    @elseif(isset($item['action']))
                        <button
                            type="button"
                            onclick="{{ $item['action'] }}"
                            class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg hover:bg-base-200 transition-all duration-200 group w-full text-left"
                            role="menuitem"
                        >
                            @if(isset($item['icon']))
                                <x-dynamic-component :component="$item['icon']" class="w-4 h-4 text-base-content/60 group-hover:text-base-content transition-colors duration-200" />
                            @endif
                            <span class="font-medium">{{ $item['label'] }}</span>
                        </button>
                    @elseif(isset($item['form']))
                        @php
                            $formId = 'form-' . uniqid();
                            $confirmMessage = $item['form']['confirm'] ?? 'Are you sure?';
                            $submitAction = "if(confirm('" . addslashes($confirmMessage) . "')) { document.getElementById('" . $formId . "').submit(); }";
                        @endphp
                        <button
                            type="button"
                            onclick="{{ $submitAction }}"
                            class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg hover:bg-base-200 transition-all duration-200 group w-full text-left"
                            role="menuitem"
                        >
                            @if(isset($item['icon']))
                                <x-dynamic-component :component="$item['icon']" class="w-4 h-4 text-base-content/60 group-hover:text-base-content transition-colors duration-200" />
                            @endif
                            <span class="font-medium">{{ $item['label'] }}</span>
                        </button>

                        <!-- Hidden form for submission -->
                        <form id="{{ $formId }}" method="POST" action="{{ $item['form']['action'] }}" style="display: none;">
                            @csrf
                            @if(isset($item['form']['method']) && strtoupper($item['form']['method']) === 'DELETE')
                                @method('DELETE')
                            @elseif(isset($item['form']['method']) && strtoupper($item['form']['method']) === 'PUT')
                                @method('PUT')
                            @elseif(isset($item['form']['method']) && strtoupper($item['form']['method']) === 'PATCH')
                                @method('PATCH')
                            @endif

                            @if(isset($item['form']['fields']))
                                @foreach($item['form']['fields'] as $field)
                                    <input type="hidden" name="{{ $field['name'] }}" value="{{ $field['value'] }}" />
                                @endforeach
                            @endif
                        </form>
                    @else
                        <span class="flex items-center gap-3 px-3 py-2.5 text-sm text-base-content/70 cursor-default" role="menuitem">
                            @if(isset($item['icon']))
                                <x-dynamic-component :component="$item['icon']" class="w-4 h-4 text-base-content/50" />
                            @endif
                            <span class="font-medium">{{ $item['label'] }}</span>
                        </span>
                    @endif
                </li>
            @endif
        @empty
            <li><span class="text-base-content/50 text-sm px-3 py-2">No menu items</span></li>
        @endforelse
    </ul>
</div>
