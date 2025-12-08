@props([
    'medication' => null,
    'submitButtonText' => 'Submit',
    'cancelUrl' => null
])

<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="md:col-span-2">
            <x-form-field
                name="name"
                label="Medication Name"
                :value="old('name', $medication?->name)"
                placeholder="e.g., Keppra, Lamotrigine"
                required
            />
        </div>

        <x-form-field
            name="dosage"
            label="Dosage"
            :value="old('dosage', $medication?->dosage)"
            placeholder="e.g., 500"
            optional
        />

        <x-form-field
            name="unit"
            label="Unit"
            type="select"
            :value="old('unit', $medication?->unit)"
            placeholder="Select unit..."
            :options="[
                'mg' => 'mg',
                'ml' => 'ml',
                'mcg' => 'mcg',
                'g' => 'g',
                'tablets' => 'tablet(s)',
                'capsules' => 'capsule(s)',
                'drops' => 'drop(s)',
                'puffs' => 'puff(s)'
            ]"
            optional
        />

        <div class="md:col-span-2">
            <x-form-field
                name="description"
                label="Description / Purpose"
                type="textarea"
                :value="old('description', $medication?->description)"
                placeholder="What is this medication for?"
                rows="2"
                optional
            />
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-form-field
            name="prescriber"
            label="Prescriber"
            :value="old('prescriber', $medication?->prescriber)"
            placeholder="Doctor's name"
            optional
        />
    </div>

    <div class="space-y-4">
        <x-form-field
            name="active"
            label="Active Medication"
            type="checkbox"
            :value="old('active', $medication?->active ?? true)"
            class="checkbox-primary"
        />

        <div>
            <x-form-field
                name="as_needed"
                label="As Needed (PRN)"
                type="checkbox"
                :value="old('as_needed', $medication?->as_needed)"
                class="checkbox-secondary"
            />
            <div class="mt-1">
                <span class="text-sm text-gray-500">Always show in schedule for PRN medications</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-form-field
            name="start_date"
            label="Start Date"
            type="date"
            :value="old('start_date', $medication?->start_date?->format('Y-m-d'))"
            optional
        />

        <x-form-field
            name="end_date"
            label="End Date"
            type="date"
            :value="old('end_date', $medication?->end_date?->format('Y-m-d'))"
            optional
        />
    </div>

        <x-form-field
            name="notes"
            label="Additional Notes"
            type="textarea"
            :value="old('notes', $medication?->notes)"
            placeholder="Any additional notes or instructions..."
            rows="3"
            optional
        />

    <div class="alert alert-info">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span>
            After {{ $medication ? 'updating' : 'adding' }} this medication, you can set up schedules for when to take it.
        </span>
    </div>

    {{-- Form Actions --}}
    <div class="flex justify-end gap-4">
        <a href="{{ $cancelUrl ?? route('medications.index') }}" class="btn btn-outline">
            Cancel
        </a>
        <button type="submit" class="btn btn-primary">
            {{ $submitButtonText }}
        </button>
    </div>
</div>
