@props([
    'vital' => null,
    'submitButtonText' => 'Submit',
    'cancelUrl' => null
])

<x-vitals.type-field :value="$vital?->type" />

<x-form-field
    name="value"
    label="Value"
    type="number"
    :value="old('value', $vital?->value)"
    placeholder="e.g., 72.5"
    step="any"
    required
/>

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
