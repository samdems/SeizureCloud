<x-layouts.app :title="__('Edit Medication')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 max-w-4xl mx-auto">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Edit Medication</h1>
            <a href="{{ route('medications.index') }}" class="btn btn-ghost">
                Back to List
            </a>
        </div>

        <form action="{{ route('medications.update', $medication) }}" method="POST" class="card bg-base-100 shadow-xl">
            <div class="card-body space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-control md:col-span-2">
                        <label for="name" class="label">
                            <span class="label-text">Medication Name *</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $medication->name) }}" required
                            class="input input-bordered">
                        @error('name')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label for="dosage" class="label">
                            <span class="label-text">Dosage</span>
                        </label>
                        <input type="text" id="dosage" name="dosage" value="{{ old('dosage', $medication->dosage) }}"
                            class="input input-bordered">
                        @error('dosage')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label for="unit" class="label">
                            <span class="label-text">Unit</span>
                        </label>
                        <select id="unit" name="unit" class="select select-bordered">
                            <option value="">Select unit...</option>
                            <option value="mg" {{ old('unit', $medication->unit) == 'mg' ? 'selected' : '' }}>mg</option>
                            <option value="ml" {{ old('unit', $medication->unit) == 'ml' ? 'selected' : '' }}>ml</option>
                            <option value="mcg" {{ old('unit', $medication->unit) == 'mcg' ? 'selected' : '' }}>mcg</option>
                            <option value="g" {{ old('unit', $medication->unit) == 'g' ? 'selected' : '' }}>g</option>
                            <option value="tablets" {{ old('unit', $medication->unit) == 'tablets' ? 'selected' : '' }}>tablet(s)</option>
                            <option value="capsules" {{ old('unit', $medication->unit) == 'capsules' ? 'selected' : '' }}>capsule(s)</option>
                            <option value="drops" {{ old('unit', $medication->unit) == 'drops' ? 'selected' : '' }}>drop(s)</option>
                            <option value="puffs" {{ old('unit', $medication->unit) == 'puffs' ? 'selected' : '' }}>puff(s)</option>
                        </select>
                        @error('unit')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="form-control md:col-span-2">
                        <label for="description" class="label">
                            <span class="label-text">Description / Purpose</span>
                        </label>
                        <textarea id="description" name="description" rows="2"
                            class="textarea textarea-bordered">{{ old('description', $medication->description) }}</textarea>
                        @error('description')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label for="prescriber" class="label">
                            <span class="label-text">Prescriber</span>
                        </label>
                        <input type="text" id="prescriber" name="prescriber" value="{{ old('prescriber', $medication->prescriber) }}"
                            class="input input-bordered">
                        @error('prescriber')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="checkbox" id="active" name="active" value="1" {{ old('active', $medication->active) ? 'checked' : '' }}
                                class="checkbox checkbox-primary">
                            <span class="label-text">Active Medication</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="checkbox" id="as_needed" name="as_needed" value="1" {{ old('as_needed', $medication->as_needed) ? 'checked' : '' }}
                                class="checkbox checkbox-secondary">
                            <span class="label-text">As Needed (Always show in schedule)</span>
                        </label>
                        <label class="label">
                            <span class="label-text-alt">Check this for medications you take on an as-needed basis</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label for="start_date" class="label">
                            <span class="label-text">Start Date</span>
                        </label>
                        <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $medication->start_date?->format('Y-m-d')) }}"
                            class="input input-bordered">
                        @error('start_date')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label for="end_date" class="label">
                            <span class="label-text">End Date</span>
                        </label>
                        <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $medication->end_date?->format('Y-m-d')) }}"
                            class="input input-bordered">
                        @error('end_date')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                </div>

                <div class="form-control">
                    <label for="notes" class="label">
                        <span class="label-text">Notes</span>
                    </label>
                    <textarea id="notes" name="notes" rows="3"
                        class="textarea textarea-bordered">{{ old('notes', $medication->notes) }}</textarea>
                    @error('notes')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <div class="flex justify-end gap-4">
                    <a href="{{ route('medications.index') }}" class="btn btn-outline">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Update Medication
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.app>
