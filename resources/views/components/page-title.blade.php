@props([
    'title',
    'actions' => [],
    'class' => '',
])

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 {{ $class }}">
    <h1 class="text-xl sm:text-2xl font-bold">{{ $title }}</h1>

    @if(count($actions) > 0)
        <div class="flex justify-between sm:justify-end sm:flex-row gap-2">
            @foreach($actions as $action)
                @if(isset($action['dropdown']))
                    <div class="dropdown dropdown-start sm:dropdown-end">
                        <div tabindex="0" role="button" class="btn {{ $action['class'] ?? 'btn-outline' }}">
                            @if(isset($action['icon']))
                                <x-dynamic-component :component="$action['icon']" class="w-4 h-4 mr-2" />
                            @endif

                            @if(isset($action['mobile_text']) && isset($action['desktop_text']))
                                {{ $action['desktop_text'] }}
                            @else
                                {{ $action['text'] ?? 'Dropdown' }}
                            @endif

                            <x-heroicon-o-chevron-down class="w-4 h-4 ml-1" />
                        </div>
                        <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-64 sm:w-52">
                            @foreach($action['dropdown'] as $item)
                                @if(isset($item['divider']) && $item['divider'])
                                    <li><hr class="my-1"></li>
                                @else
                                    <li>
                                        <a href="{{ $item['href'] ?? '#' }}"
                                           @if(isset($item['onclick'])) onclick="{{ $item['onclick'] }}" @endif>
                                            @if(isset($item['icon']))
                                                <x-dynamic-component :component="$item['icon']" class="w-4 h-4" />
                                            @endif
                                            {{ $item['text'] }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @else
                    <a
                        href="{{ $action['href'] }}"
                        class="btn {{ $action['class'] ?? 'btn-primary' }}"
                        @if(isset($action['wire:navigate']) && $action['wire:navigate']) wire:navigate @endif
                        @if(isset($action['onclick'])) onclick="{{ $action['onclick'] }}" @endif
                        @if(isset($action['title'])) title="{{ $action['title'] }}" @endif
                    >
                        @if(isset($action['icon']))
                            <x-dynamic-component :component="$action['icon']" class="h-4 w-4 mr-2" />
                        @endif

                        @if(isset($action['mobile_text']) && isset($action['desktop_text']))
                            {{ $action['desktop_text'] }}
                        @else
                            {{ $action['text'] ?? 'Button' }}
                        @endif
                    </a>
                @endif
            @endforeach
        </div>
    @endif
</div>
