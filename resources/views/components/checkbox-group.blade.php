@props([
    'name',
    'label',
    'options' => [],
    'values' => [],
    'columns' => 'md:grid-cols-2 lg:grid-cols-3',
    'checkboxSize' => 'checkbox-sm',
    'checkboxClass' => 'checkbox-primary',
    'required' => false,
    'optional' => false,
    'helpText' => null,
    'wrapperClass' => 'mb-4'
])

@php
    $selectedValues = old($name, $values);
    if (!is_array($selectedValues)) {
        $selectedValues = [];
    }
@endphp

<div class="form-control {{ $wrapperClass }}">
    <label class="block text-sm font-semibold text-base-content mb-2">
        {{ $label }}
        @if($optional)
            <span class="text-sm text-gray-500 ml-1">(Optional)</span>
        @endif
        @if($helpText)
            <span class="block text-xs text-gray-500 mt-1 font-normal">{{ $helpText }}</span>
        @endif
    </label>

    <div class="grid grid-cols-1 {{ $columns }} gap-2">
        @foreach($options as $value => $text)
            <label class="cursor-pointer label justify-start gap-2">
                <input
                    type="checkbox"
                    name="{{ $name }}[]"
                    value="{{ $value }}"
                    class="checkbox {{ $checkboxClass }} {{ $checkboxSize }}"
                    {{ in_array($value, $selectedValues) ? 'checked' : '' }}
                    {{ $required ? 'required' : '' }}
                >
                <span class="label-text text-sm">{{ $text }}</span>
            </label>
        @endforeach
    </div>

    @error($name)
        <div class="label">
            <span class="label-text-alt text-error">{{ $message }}</span>
        </div>
    @enderror
</div>
