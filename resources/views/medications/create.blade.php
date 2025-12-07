<x-layouts.app :title="__('Add Medication')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 max-w-4xl mx-auto">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Add Medication</h1>
            <a href="{{ route('medications.index') }}" class="btn btn-ghost">
                Back to List
            </a>
        </div>

        <form action="{{ route('medications.store') }}" method="POST" class="card bg-base-100 shadow-xl">
            <div class="card-body space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-control md:col-span-2">
                        <label for="name" class="label">
                            <span class="label-text">Medication Name *</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="input input-bordered" placeholder="e.g., Keppra, Lamotrigine">
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
                        <input type="text" id="dosage" name="dosage" value="{{ old('dosage') }}"
                            class="input input-bordered" placeholder="e.g., 500">
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
                            <option value="mg" {{ old('unit') == 'mg' ? 'selected' : '' }}>mg</option>
                            <option value="ml" {{ old('unit') == 'ml' ? 'selected' : '' }}>ml</option>
                            <option value="mcg" {{ old('unit') == 'mcg' ? 'selected' : '' }}>mcg</option>
                            <option value="g" {{ old('unit') == 'g' ? 'selected' : '' }}>g</option>
                            <option value="tablets" {{ old('unit') == 'tablets' ? 'selected' : '' }}>tablet(s)</option>
                            <option value="capsules" {{ old('unit') == 'capsules' ? 'selected' : '' }}>capsule(s)</option>
                            <option value="drops" {{ old('unit') == 'drops' ? 'selected' : '' }}>drop(s)</option>
                            <option value="puffs" {{ old('unit') == 'puffs' ? 'selected' : '' }}>puff(s)</option>
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
                            class="textarea textarea-bordered" placeholder="What is this medication for?">{{ old('description') }}</textarea>
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
                        <input type="text" id="prescriber" name="prescriber" value="{{ old('prescriber') }}"
                            class="input input-bordered" placeholder="Doctor's name">
                        @error('prescriber')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="checkbox" id="active" name="active" value="1" {{ old('active', true) ? 'checked' : '' }}
                                class="checkbox checkbox-primary">
                            <span class="label-text">Active Medication</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="checkbox" id="as_needed" name="as_needed" value="1" {{ old('as_needed') ? 'checked' : '' }}
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
                        <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}"
                            class="input input-bordered">
                        @error('start_date')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label for="end_date" class="label">
                            <span class="label-text">End Date (if applicable)</span>
                        </label>
                        <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}"
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
                        class="textarea textarea-bordered" placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <div class="alert alert-info">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>After adding the medication, you can set up schedules for when to take it.</span>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="{{ route('medications.index') }}" class="btn btn-outline">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Add Medication
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.app>
