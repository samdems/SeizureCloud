@props(['log', 'compact' => false])

@if($log && !$log->skipped)
    <div class="flex items-center gap-2 text-sm">
        @if($log->intended_time)
            @php
                $timeDifference = $log->getTimeDifference();
                $lateThreshold = $log->intended_time->copy()->addMinutes(10);
                $earlyThreshold = $log->intended_time->copy()->subMinutes(10);

                $isLate = $log->taken_at->gt($lateThreshold);
                $isEarly = $log->taken_at->lt($earlyThreshold);
            @endphp

            @if($compact)
                @if($timeDifference === 'On time')
                    <span class="badge badge-success badge-xs">
                        <x-heroicon-o-clock class="w-3 h-3 mr-1" />
                        On time
                    </span>
                @elseif($isLate)
                    <span class="badge badge-warning badge-xs">
                        <x-heroicon-o-exclamation-triangle class="w-3 h-3 mr-1" />
                        {{ $timeDifference }}
                    </span>
                @elseif($isEarly)
                    <span class="badge badge-info badge-xs">
                        <x-heroicon-o-clock class="w-3 h-3 mr-1" />
                        {{ $timeDifference }}
                    </span>
                @endif
            @else
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <span class="text-base-content/60">Intended:</span>
                        <span class="font-medium">{{ $log->intended_time->format('g:i A') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-base-content/60">Taken:</span>
                        <span class="font-medium">{{ $log->taken_at->format('g:i A') }}</span>
                        @if($timeDifference === 'On time')
                            <span class="badge badge-success badge-sm">
                                <x-heroicon-o-check class="w-3 h-3 mr-1" />
                                On time
                            </span>
                        @elseif($isLate)
                            <span class="badge badge-warning badge-sm">
                                <x-heroicon-o-exclamation-triangle class="w-3 h-3 mr-1" />
                                {{ $timeDifference }}
                            </span>
                        @elseif($isEarly)
                            <span class="badge badge-info badge-sm">
                                <x-heroicon-o-clock class="w-3 h-3 mr-1" />
                                {{ $timeDifference }}
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        @else
            <div class="flex items-center gap-1 text-base-content/60">
                <x-heroicon-o-clock class="w-4 h-4" />
                <span>{{ $log->taken_at->format('g:i A') }}</span>
            </div>
        @endif
    </div>
@elseif($log && $log->skipped)
    <div class="flex items-center gap-2 text-sm">
        <span class="badge badge-error badge-sm">
            <x-heroicon-o-x-mark class="w-3 h-3 mr-1" />
            Skipped
        </span>
        @if($log->skip_reason)
            <span class="text-base-content/60">{{ $log->skip_reason }}</span>
        @endif
    </div>
@endif
