<x-layouts.app :title="__('Vital Thresholds')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 max-w-6xl mx-auto">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Vital Thresholds</h1>
                <p class="text-base-content/60">Set normal ranges for your vital signs to get alerts when values are too low or too high</p>
            </div>
            <a href="{{ route('vitals.index') }}" class="btn btn-ghost">
                Back to Vitals
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="alert alert-info">
            <x-heroicon-o-information-circle class="stroke-current shrink-0 w-6 h-6" />
            <div>
                <h3 class="font-bold">How Thresholds Work</h3>
                <div class="text-sm">
                    <p>• <strong>Low Threshold:</strong> Values below this will be marked as "Too Low" (shown in red)</p>
                    <p>• <strong>High Threshold:</strong> Values above this will be marked as "Too High" (shown in orange)</p>
                    <p>• <strong>Normal Range:</strong> Values between your thresholds will be marked as "Normal" (shown in green)</p>
                    <p>• Leave thresholds empty if you don't want alerts for that direction</p>
                </div>
            </div>
        </div>

        <!-- Status Examples -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title">Visual Status Examples</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-error bg-opacity-10 border-l-4 border-error p-3 rounded">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-arrow-down class="h-4 w-4 text-error" />
                            <span class="badge badge-error">Too Low</span>
                        </div>
                        <p class="text-sm text-error mt-1">Values below your low threshold</p>
                    </div>

                    <div class="bg-success bg-opacity-10 border-l-4 border-success p-3 rounded">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-check-circle class="h-4 w-4 text-success" />
                            <span class="badge badge-success">Normal</span>
                        </div>
                        <p class="text-sm text-success mt-1">Values within your normal range</p>
                    </div>

                    <div class="bg-error bg-opacity-10 border-l-4 border-error p-3 rounded">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-arrow-up class="h-4 w-4 text-error" />
                            <span class="badge badge-error">Too High</span>
                        </div>
                        <p class="text-sm text-error mt-1">Values above your high threshold</p>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('vitals.thresholds.update') }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($vitalTypes as $vitalType)
                    @php
                        $threshold = $thresholds->get($vitalType);
                        $defaults = App\Models\VitalTypeThreshold::getDefaultThresholds();
                        $defaultThreshold = $defaults[$vitalType] ?? ['low' => null, 'high' => null];
                    @endphp

                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h3 class="card-title text-lg">{{ $vitalType }}</h3>

                            <input type="hidden" name="thresholds[{{ $loop->index }}][vital_type]" value="{{ $vitalType }}">

                            <div class="form-control">
                                <label class="label cursor-pointer">
                                    <span class="label-text">Enable threshold monitoring</span>
                                    <input type="hidden" name="thresholds[{{ $loop->index }}][is_active]" value="0">
                                    <input type="checkbox" name="thresholds[{{ $loop->index }}][is_active]" value="1"
                                           class="checkbox checkbox-primary"
                                           {{ ($threshold?->is_active ?? true) ? 'checked' : '' }}
                                           onchange="toggleThresholdInputs{{ $loop->index }}()">
                                </label>
                            </div>

                            <div id="thresholdInputs{{ $loop->index }}" class="{{ !($threshold?->is_active ?? true) ? 'opacity-50 pointer-events-none' : '' }}">
                                @if($vitalType === 'Blood Pressure')
                                    <!-- Blood Pressure Specific Fields -->
                                    <div class="space-y-4">
                                        <h4 class="font-medium text-sm">Systolic Pressure (Top Number)</h4>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="form-control">
                                                <label class="label">
                                                    <span class="label-text">Low Threshold</span>
                                                    <span class="label-text-alt text-error">Too Low Below</span>
                                                </label>
                                                <input type="number"
                                                       name="thresholds[{{ $loop->index }}][systolic_low_threshold]"
                                                       value="{{ $threshold?->systolic_low_threshold ?? ($defaultThreshold['systolic_low'] ?? '') }}"
                                                       class="input input-bordered input-sm"
                                                       step="any"
                                                       placeholder="Min systolic">
                                            </div>

                                            <div class="form-control">
                                                <label class="label">
                                                    <span class="label-text">High Threshold</span>
                                                    <span class="label-text-alt text-error">Too High Above</span>
                                                </label>
                                                <input type="number"
                                                       name="thresholds[{{ $loop->index }}][systolic_high_threshold]"
                                                       value="{{ $threshold?->systolic_high_threshold ?? ($defaultThreshold['systolic_high'] ?? '') }}"
                                                       class="input input-bordered input-sm"
                                                       step="any"
                                                       placeholder="Max systolic">
                                            </div>
                                        </div>

                                        <h4 class="font-medium text-sm">Diastolic Pressure (Bottom Number)</h4>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="form-control">
                                                <label class="label">
                                                    <span class="label-text">Low Threshold</span>
                                                    <span class="label-text-alt text-error">Too Low Below</span>
                                                </label>
                                                <input type="number"
                                                       name="thresholds[{{ $loop->index }}][diastolic_low_threshold]"
                                                       value="{{ $threshold?->diastolic_low_threshold ?? ($defaultThreshold['diastolic_low'] ?? '') }}"
                                                       class="input input-bordered input-sm"
                                                       step="any"
                                                       placeholder="Min diastolic">
                                            </div>

                                            <div class="form-control">
                                                <label class="label">
                                                    <span class="label-text">High Threshold</span>
                                                    <span class="label-text-alt text-error">Too High Above</span>
                                                </label>
                                                <input type="number"
                                                       name="thresholds[{{ $loop->index }}][diastolic_high_threshold]"
                                                       value="{{ $threshold?->diastolic_high_threshold ?? ($defaultThreshold['diastolic_high'] ?? '') }}"
                                                       class="input input-bordered input-sm"
                                                       step="any"
                                                       placeholder="Max diastolic">
                                            </div>
                                        </div>

                                        <div class="alert alert-info">
                                            <x-heroicon-o-light-bulb class="stroke-current shrink-0 w-4 h-4" />
                                            <div class="text-sm">
                                                <strong>Recommended BP ranges:</strong><br>
                                                Systolic: {{ $defaultThreshold['systolic_low'] ?? 90 }} - {{ $defaultThreshold['systolic_high'] ?? 140 }} mmHg<br>
                                                Diastolic: {{ $defaultThreshold['diastolic_low'] ?? 60 }} - {{ $defaultThreshold['diastolic_high'] ?? 90 }} mmHg
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- Regular Vital Fields -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="form-control">
                                            <label class="label">
                                                <span class="label-text">Low Threshold</span>
                                                <span class="label-text-alt text-error">Too Low Below</span>
                                            </label>
                                            <input type="number"
                                                   name="thresholds[{{ $loop->index }}][low_threshold]"
                                                   value="{{ $threshold?->low_threshold ?? $defaultThreshold['low'] }}"
                                                   class="input input-bordered input-sm"
                                                   step="any"
                                                   placeholder="Min value">
                                        </div>

                                        <div class="form-control">
                                            <label class="label">
                                                <span class="label-text">High Threshold</span>
                                                <span class="label-text-alt text-warning">Too High Above</span>
                                            </label>
                                            <input type="number"
                                                   name="thresholds[{{ $loop->index }}][high_threshold]"
                                                   value="{{ $threshold?->high_threshold ?? $defaultThreshold['high'] }}"
                                                   class="input input-bordered input-sm"
                                                   step="any"
                                                   placeholder="Max value">
                                        </div>
                                    </div>

                                    @if($defaultThreshold['low'] !== null || $defaultThreshold['high'] !== null)
                                        <div class="alert alert-info mt-2">
                                            <x-heroicon-o-light-bulb class="stroke-current shrink-0 w-4 h-4" />
                                            <div class="text-sm">
                                                <strong>Recommended ranges:</strong>
                                                @if($defaultThreshold['low'] !== null && $defaultThreshold['high'] !== null)
                                                    {{ $defaultThreshold['low'] }} - {{ $defaultThreshold['high'] }}
                                                @elseif($defaultThreshold['low'] !== null)
                                                    Above {{ $defaultThreshold['low'] }}
                                                @elseif($defaultThreshold['high'] !== null)
                                                    Below {{ $defaultThreshold['high'] }}
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mt-3">
                                        <div class="text-xs text-base-content/60">
                                            <span class="badge badge-success badge-xs">Normal</span>
                                            @if($threshold?->low_threshold || $defaultThreshold['low'])
                                                {{ $threshold?->low_threshold ?? $defaultThreshold['low'] ?? '?' }}
                                            @else
                                                No min
                                            @endif
                                            -
                                            @if($threshold?->high_threshold || $defaultThreshold['high'])
                                                {{ $threshold?->high_threshold ?? $defaultThreshold['high'] ?? '?' }}
                                            @else
                                                No max
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <script>
                                function toggleThresholdInputs{{ $loop->index }}() {
                                    const checkbox = document.querySelector('input[name="thresholds[{{ $loop->index }}][is_active]"][type="checkbox"]');
                                    const container = document.getElementById('thresholdInputs{{ $loop->index }}');

                                    if (checkbox.checked) {
                                        container.classList.remove('opacity-50', 'pointer-events-none');
                                    } else {
                                        container.classList.add('opacity-50', 'pointer-events-none');
                                    }
                                }
                            </script>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card bg-base-100 shadow-xl mt-6">
                <div class="card-body">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h3 class="font-bold">Save Your Threshold Settings</h3>
                            <p class="text-sm text-base-content/60">These settings will be applied to all new vital readings</p>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" onclick="resetToDefaults()" class="btn btn-outline">
                                Reset to Defaults
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <x-heroicon-o-check class="h-5 w-5" />
                                Save Thresholds
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function resetToDefaults() {
            if (confirm('Reset all thresholds to recommended default values? This will overwrite your current settings.')) {
                const defaults = @json(App\Models\VitalTypeThreshold::getDefaultThresholds());

                @foreach($vitalTypes as $index => $vitalType)
                    const vitalDefaults = defaults['{{ $vitalType }}'] || {};

                    @if($vitalType === 'Blood Pressure')
                        // Reset blood pressure specific fields
                        const systolicLowInput = document.querySelector('input[name="thresholds[{{ $index }}][systolic_low_threshold]"]');
                        if (systolicLowInput) {
                            systolicLowInput.value = vitalDefaults.systolic_low || '';
                        }

                        const systolicHighInput = document.querySelector('input[name="thresholds[{{ $index }}][systolic_high_threshold]"]');
                        if (systolicHighInput) {
                            systolicHighInput.value = vitalDefaults.systolic_high || '';
                        }

                        const diastolicLowInput = document.querySelector('input[name="thresholds[{{ $index }}][diastolic_low_threshold]"]');
                        if (diastolicLowInput) {
                            diastolicLowInput.value = vitalDefaults.diastolic_low || '';
                        }

                        const diastolicHighInput = document.querySelector('input[name="thresholds[{{ $index }}][diastolic_high_threshold]"]');
                        if (diastolicHighInput) {
                            diastolicHighInput.value = vitalDefaults.diastolic_high || '';
                        }
                    @else
                        // Reset regular vital fields
                        const lowInput = document.querySelector('input[name="thresholds[{{ $index }}][low_threshold]"]');
                        if (lowInput) {
                            lowInput.value = vitalDefaults.low || '';
                        }

                        const highInput = document.querySelector('input[name="thresholds[{{ $index }}][high_threshold]"]');
                        if (highInput) {
                            highInput.value = vitalDefaults.high || '';
                        }
                    @endif

                    // Enable monitoring
                    const activeCheckbox = document.querySelector('input[name="thresholds[{{ $index }}][is_active]"][type="checkbox"]');
                    if (activeCheckbox) {
                        activeCheckbox.checked = true;
                        toggleThresholdInputs{{ $index }}();
                    }
                @endforeach
            }
        }
    </script>
</x-layouts.app>
