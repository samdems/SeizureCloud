<x-layouts.app :title="__('Vital Details')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Vital Details</h1>
            <div class="flex gap-2">
                <a href="{{ route('vitals.edit', $vital) }}" class="btn btn-primary">Edit</a>
                <a href="{{ route('vitals.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>

        @php
            $status = $vital->getStatus();
            $cardClass = match($status) {
                'too_low' => 'bg-error bg-opacity-5 border-2 border-error',
                'too_high' => 'bg-error bg-opacity-5 border-2 border-error',
                default => 'bg-base-100'
            };
        @endphp

        <div class="card {{ $cardClass }} shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="card-title text-xl">{{ $vital->type }}</h2>
                    @if($status !== 'normal')
                        <div class="alert alert-error py-2 px-4">
                            <x-heroicon-o-exclamation-triangle class="stroke-current shrink-0 w-5 h-5" />
                            <span class="font-semibold">{{ $vital->getStatusText() }}</span>
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="label">
                            <span class="label-text font-semibold">Value</span>
                        </label>
                        <div class="flex items-center gap-3">
                            <div class="text-2xl font-bold text-primary">{{ $vital->getFormattedValue() }}</div>
                            @if($vital->getStatus() !== 'normal')
                                @if($vital->getStatus() === 'too_low')
                                    <x-heroicon-o-arrow-down class="h-6 w-6 text-error" />
                                @else
                                    <x-heroicon-o-arrow-up class="h-6 w-6 text-error" />
                                @endif
                            @endif
                        </div>
                        <span class="badge {{ $vital->getStatusBadgeClass() }} badge-lg mt-2">
                            {{ $vital->getStatusText() }}
                        </span>

                        @if($vital->isBloodPressure() && $vital->systolic_value && $vital->diastolic_value)
                            <div class="grid grid-cols-2 gap-4 mt-4 p-3 bg-base-200 rounded-lg">
                                <div class="text-center">
                                    <div class="text-lg font-semibold text-primary">{{ $vital->systolic_value }}</div>
                                    <div class="text-xs text-base-content/60">Systolic</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-semibold text-secondary">{{ $vital->diastolic_value }}</div>
                                    <div class="text-xs text-base-content/60">Diastolic</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="label">
                            <span class="label-text font-semibold">Recorded At</span>
                        </label>
                        <div class="text-lg">{{ $vital->recorded_at->format('M j, Y g:i A') }}</div>
                    </div>
                </div>

                @if($vital->getStatus() !== 'normal' || $vital->low_threshold !== null || $vital->high_threshold !== null)
                    <div class="mt-6">
                        <label class="label">
                            <span class="label-text font-semibold">Threshold Information</span>
                        </label>
                        <div class="bg-base-200 p-4 rounded-lg">
                            @php
                                $lowThreshold = $vital->low_threshold ?? $vital->getDefaultLowThreshold();
                                $highThreshold = $vital->high_threshold ?? $vital->getDefaultHighThreshold();
                            @endphp

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium">Low Threshold:</span>
                                    {{ $lowThreshold ?? 'Not set' }}
                                </div>
                                <div>
                                    <span class="font-medium">High Threshold:</span>
                                    {{ $highThreshold ?? 'Not set' }}
                                </div>
                            </div>

                            @if($lowThreshold || $highThreshold)
                                <div class="mt-2 text-sm">
                                    <span class="font-medium">Normal Range:</span>
                                    @if($lowThreshold && $highThreshold)
                                        {{ $lowThreshold }} - {{ $highThreshold }}
                                    @elseif($lowThreshold)
                                        Above {{ $lowThreshold }}
                                    @elseif($highThreshold)
                                        Below {{ $highThreshold }}
                                    @endif
                                </div>
                            @endif

                            @if($vital->getStatus() !== 'normal')
                                <div class="alert alert-error mt-3">
                                    <x-heroicon-o-exclamation-triangle class="stroke-current shrink-0 w-6 h-6" />
                                    <span>
                                        @if($vital->getStatus() === 'too_low')
                                            This value is below the recommended minimum{{ $lowThreshold ? ' of ' . $lowThreshold : '' }}.
                                        @else
                                            This value is above the recommended maximum{{ $highThreshold ? ' of ' . $highThreshold : '' }}.
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if($vital->notes)
                    <div class="mt-6">
                        <label class="label">
                            <span class="label-text font-semibold">Notes</span>
                        </label>
                        <div class="bg-base-200 p-4 rounded-lg">
                            {{ $vital->notes }}
                        </div>
                    </div>
                @endif

                <div class="card-actions justify-end mt-6">
                    <form action="{{ route('vitals.destroy', $vital) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-error" onclick="return confirm('Are you sure you want to delete this vital record?')">
                            Delete Vital
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
