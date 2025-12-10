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

                                        <div class="alert">
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
                                        <div class="alert  mt-2">
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
</x-layouts.app>
