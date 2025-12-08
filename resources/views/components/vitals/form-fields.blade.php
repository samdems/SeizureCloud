@props([
    'vital' => null,
    'submitButtonText' => 'Submit',
    'cancelUrl' => null
])

@php
    $user = Auth::user();
    $currentType = old('type', $vital?->type);
    $userThreshold = null;

    if ($currentType) {
        $userThreshold = $user->vitalTypeThresholds()
            ->where('vital_type', $currentType)
            ->where('is_active', true)
            ->first();

        if (!$userThreshold) {
            $defaults = App\Models\VitalTypeThreshold::getDefaultThresholds();
            $defaultThreshold = $defaults[$currentType] ?? null;
        }
    }
@endphp

<x-vitals.type-field :value="$vital?->type" />

<!-- Threshold Info Display -->
<div id="thresholdInfo" class="alert alert-info mb-4" style="display: none;">
    <x-heroicon-o-information-circle class="stroke-current shrink-0 w-6 h-6" />
    <div>
        <h4 class="font-bold">Normal Range for <span id="selectedType"></span></h4>
        <div class="text-sm">
            <span id="rangeText">Set up thresholds to see normal ranges</span>
            <a href="{{ route('vitals.thresholds') }}" class="link link-primary ml-2">Manage Thresholds</a>
        </div>
    </div>
</div>

<x-form-field
    name="value"
    id="vitalValue"
    label="Value"
    type="text"
    :value="old('value', $vital ? $vital->getFormattedValue() : '')"
    placeholder="e.g., 72.5 or 120/80"
    required
    onchange="checkValueStatus()"
    oninput="checkValueStatus()"
/>

<!-- Blood Pressure Help Text -->
<div id="bpHelpText" style="display: none;" class="text-sm text-base-content/60 mb-2">
    <x-heroicon-o-information-circle class="w-4 h-4 inline mr-1" />
    Enter as "systolic/diastolic" format (e.g., 120/80)
</div>

<!-- Value Status Indicator -->
<div id="valueStatus" class="mb-4" style="display: none;"></div>

<!-- Optional Threshold Overrides -->
<div class="collapse collapse-arrow border border-base-300 bg-base-100 mb-4">
    <input type="checkbox" />
    <div class="collapse-title text-sm font-medium">
        Advanced: Override thresholds for this reading
    </div>
    <div class="collapse-content">
        <div class="grid grid-cols-2 gap-4">
            <x-form-field
                name="low_threshold"
                label="Low Threshold (Optional)"
                type="number"
                :value="old('low_threshold', $vital?->low_threshold)"
                step="any"
                placeholder="Override low threshold"
                optional
            />

            <x-form-field
                name="high_threshold"
                label="High Threshold (Optional)"
                type="number"
                :value="old('high_threshold', $vital?->high_threshold)"
                step="any"
                placeholder="Override high threshold"
                optional
            />
        </div>
        <div class="text-xs text-base-content/60 mt-2">
            Leave empty to use your default thresholds for this vital type.
        </div>
    </div>
</div>

<x-form-field
    name="recorded_at"
    label="Recorded At"
    type="datetime-local"
    :value="old('recorded_at', $vital?->recorded_at?->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i'))"
    required
/>

<x-form-field
    name="notes"
    label="Notes"
    type="textarea"
    :value="old('notes', $vital?->notes)"
    placeholder="Any additional notes about this vital reading..."
    rows="3"
    wrapper-class="mb-6"
    optional
/>

<div class="card-actions justify-end gap-2">
    <a href="{{ $cancelUrl ?? route('vitals.index') }}" class="btn btn-ghost">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ $submitButtonText }}</button>
</div>

<script>
    // Threshold data for JavaScript
    const thresholdData = @json(
        $user->vitalTypeThresholds()
            ->where('is_active', true)
            ->get()
            ->keyBy('vital_type')
            ->map(function($threshold) {
                return [
                    'low' => $threshold->low_threshold,
                    'high' => $threshold->high_threshold
                ];
            })
    );

    const defaultThresholds = @json(App\Models\VitalTypeThreshold::getDefaultThresholds());

    function updateThresholdDisplay() {
        const typeSelect = document.getElementById('type_select');
        const typeInput = document.getElementById('type');
        const thresholdInfo = document.getElementById('thresholdInfo');
        const selectedTypeSpan = document.getElementById('selectedType');
        const rangeText = document.getElementById('rangeText');
        const bpHelpText = document.getElementById('bpHelpText');
        const valueInput = document.getElementById('vitalValue');

        let selectedType = typeSelect.value === 'custom' ? typeInput.value : typeSelect.value;

        // Show/hide blood pressure help text
        if (selectedType === 'Blood Pressure') {
            bpHelpText.style.display = 'block';
            if (valueInput) {
                valueInput.placeholder = 'e.g., 120/80';
            }
        } else {
            bpHelpText.style.display = 'none';
            if (valueInput) {
                valueInput.placeholder = 'e.g., 72.5';
            }
        }

        if (selectedType && selectedType !== 'custom') {
            selectedTypeSpan.textContent = selectedType;

            // Get user threshold or default
            let threshold = thresholdData[selectedType];
            if (!threshold) {
                threshold = defaultThresholds[selectedType];
            }

            if (threshold && (threshold.low !== null || threshold.high !== null)) {
                let rangeStr = '';
                if (threshold.low !== null && threshold.high !== null) {
                    rangeStr = `Normal range: ${threshold.low} - ${threshold.high}`;
                } else if (threshold.low !== null) {
                    rangeStr = `Should be above ${threshold.low}`;
                } else if (threshold.high !== null) {
                    rangeStr = `Should be below ${threshold.high}`;
                }

                rangeText.innerHTML = rangeStr;
                thresholdInfo.style.display = 'flex';
            } else {
                thresholdInfo.style.display = 'none';
            }
        } else {
            thresholdInfo.style.display = 'none';
        }

        checkValueStatus();
    }

    function checkValueStatus() {
        const typeSelect = document.getElementById('type_select');
        const typeInput = document.getElementById('type');
        const valueInput = document.querySelector('input[name="value"]');
        const statusDiv = document.getElementById('valueStatus');

        let selectedType = typeSelect.value === 'custom' ? typeInput.value : typeSelect.value;
        let inputValue = valueInput.value.trim();
        let value;

        // Handle blood pressure parsing
        if (selectedType === 'Blood Pressure' && inputValue.includes('/')) {
            const parts = inputValue.split('/');
            if (parts.length === 2 && !isNaN(parts[0]) && !isNaN(parts[1])) {
                // Use systolic as primary value for threshold checking
                value = parseFloat(parts[0]);
            } else {
                statusDiv.style.display = 'none';
                return;
            }
        } else {
            value = parseFloat(inputValue);
        }

        if (selectedType && !isNaN(value)) {
            let threshold;

            // For blood pressure, use the Blood Pressure threshold
            threshold = thresholdData[selectedType] || defaultThresholds[selectedType];

            if (threshold) {
                let status = 'normal';
                let statusText = 'Normal';
                let statusClass = 'badge-success';
                let bgClass = '';

                if (threshold.low !== null && value < threshold.low) {
                    status = 'too_low';
                    statusText = 'Too Low';
                    statusClass = 'badge-error';
                    bgClass = 'bg-error bg-opacity-20';
                } else if (threshold.high !== null && value > threshold.high) {
                    status = 'too_high';
                    statusText = 'Too High';
                    statusClass = 'badge-error';
                    bgClass = 'bg-error bg-opacity-20';
                }

                let statusMessage = '';
                if (selectedType === 'Blood Pressure') {
                    statusMessage = status === 'too_low' ? `Systolic pressure is below the recommended minimum of ${threshold.systolic_low || threshold.low}` :
                                  status === 'too_high' ? `Systolic pressure is above the recommended maximum of ${threshold.systolic_high || threshold.high}` :
                                  'Blood pressure appears to be within normal range';
                } else {
                    statusMessage = status === 'too_low' ? `This value is below the recommended minimum of ${threshold.low}` :
                                  status === 'too_high' ? `This value is above the recommended maximum of ${threshold.high}` :
                                  'This value is within the normal range';
                }

                statusDiv.innerHTML = `
                    <div class="alert ${bgClass} ${status !== 'normal' ? 'border-2' : ''}">
                        <div class="flex items-center gap-2">
                            <span class="badge ${statusClass}">${statusText}</span>
                            <span class="text-sm">${statusMessage}</span>
                        </div>
                    </div>
                `;
                statusDiv.style.display = 'block';
            } else {
                statusDiv.style.display = 'none';
            }
        } else {
            statusDiv.style.display = 'none';
        }
    }

    // Listen for type changes
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type_select');
        const typeInput = document.getElementById('type');

        if (typeSelect) {
            typeSelect.addEventListener('change', updateThresholdDisplay);
        }

        if (typeInput) {
            typeInput.addEventListener('input', updateThresholdDisplay);
        }

        // Initial update
        updateThresholdDisplay();
    });
</script>
