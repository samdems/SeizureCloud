<x-layouts.app :title="__('Edit Vital')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 max-w-4xl mx-auto">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Edit Vital</h1>
            <a href="{{ route('vitals.index') }}" class="btn btn-ghost">
                Back to List
            </a>
        </div>

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <form action="{{ route('vitals.update', $vital) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-control mb-4">
                        <label class="label" for="type_select">
                            <span class="label-text font-semibold">Type</span>
                        </label>
                        <select id="type_select"
                                class="select select-bordered @error('type') select-error @enderror"
                                onchange="toggleCustomType()">
                            <option value="">Select a vital type</option>
                            @foreach(config('app.vital_types') as $vitalType)
                                <option value="{{ $vitalType }}" {{ old('type', $vital->type) == $vitalType ? 'selected' : '' }}>
                                    {{ $vitalType }}
                                </option>
                            @endforeach
                            <option value="custom" {{ old('type', $vital->type) && !in_array(old('type', $vital->type), config('app.vital_types')) ? 'selected' : '' }}>
                                Custom Type
                            </option>
                        </select>

                        <input type="text"
                               id="type"
                               name="type"
                               value="{{ old('type', $vital->type) }}"
                               class="input input-bordered mt-2 @error('type') input-error @enderror {{ old('type', $vital->type) && !in_array(old('type', $vital->type), config('app.vital_types')) ? '' : 'hidden' }}"
                               placeholder="Enter custom vital type"
                               style="display: {{ old('type', $vital->type) && !in_array(old('type', $vital->type), config('app.vital_types')) ? 'block' : 'none' }}">

                        @error('type')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="form-control mb-4">
                        <label class="label" for="value">
                            <span class="label-text font-semibold">Value</span>
                        </label>
                        <input type="number"
                               step="any"
                               id="value"
                               name="value"
                               value="{{ old('value', $vital->value) }}"
                               class="input input-bordered @error('value') input-error @enderror"
                               placeholder="e.g., 72.5"
                               required>
                        @error('value')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="form-control mb-4">
                        <label class="label" for="recorded_at">
                            <span class="label-text font-semibold">Recorded At</span>
                        </label>
                        <input type="datetime-local"
                               id="recorded_at"
                               name="recorded_at"
                               value="{{ old('recorded_at', $vital->recorded_at->format('Y-m-d\TH:i')) }}"
                               class="input input-bordered @error('recorded_at') input-error @enderror"
                               required>
                        @error('recorded_at')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="form-control mb-6">
                        <label class="label" for="notes">
                            <span class="label-text font-semibold">Notes</span>
                            <span class="label-text-alt">Optional</span>
                        </label>
                        <textarea id="notes"
                                  name="notes"
                                  class="textarea textarea-bordered @error('notes') textarea-error @enderror"
                                  placeholder="Any additional notes about this vital reading..."
                                  rows="3">{{ old('notes', $vital->notes) }}</textarea>
                        @error('notes')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="card-actions justify-end gap-2">
                        <a href="{{ route('vitals.show', $vital) }}" class="btn btn-ghost">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Vital</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleCustomType() {
            const select = document.getElementById('type_select');
            const input = document.getElementById('type');

            if (select.value === 'custom') {
                input.style.display = 'block';
                input.classList.remove('hidden');
                input.required = true;
                input.value = '';
                input.focus();
            } else {
                input.style.display = 'none';
                input.classList.add('hidden');
                input.required = false;
                input.value = select.value;
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('type_select');
            const input = document.getElementById('type');

            // If there's an old value that's not in the predefined types, show custom input
            if (input.value && !Array.from(select.options).some(option => option.value === input.value)) {
                select.value = 'custom';
                input.style.display = 'block';
                input.classList.remove('hidden');
                input.required = true;
            } else if (input.value) {
                select.value = input.value;
            }
        });
    </script>
</x-layouts.app>
