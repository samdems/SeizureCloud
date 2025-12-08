@props([
    'value' => null
])

@php
    $currentValue = old('type', $value);
    $isCustomType = $currentValue && !in_array($currentValue, config('app.vital_types'));
@endphp

<div class="form-control mb-4">
    <label class="block text-sm font-semibold text-base-content mb-2" for="type_select">
        Type
    </label>
    <select id="type_select"
            class="select select-bordered @error('type') select-error @enderror"
            onchange="toggleCustomType()">
        <option value="">Select a vital type</option>
        @foreach(config('app.vital_types') as $vitalType)
            <option value="{{ $vitalType }}" {{ $currentValue == $vitalType ? 'selected' : '' }}>
                {{ $vitalType }}
            </option>
        @endforeach
        <option value="custom" {{ $isCustomType ? 'selected' : '' }}>
            Custom Type
        </option>
    </select>

    <input type="text"
           id="type"
           name="type"
           value="{{ $currentValue }}"
           class="input input-bordered mt-2 @error('type') input-error @enderror {{ $isCustomType ? '' : 'hidden' }}"
           placeholder="Enter custom vital type"
           style="display: {{ $isCustomType ? 'block' : 'none' }}">

    @error('type')
        <label class="label">
            <span class="label-text-alt text-error">{{ $message }}</span>
        </label>
    @enderror
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
