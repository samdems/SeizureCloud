@props([
    'name',
    'label',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'optional' => false,
    'options' => [],
    'rows' => 3,
    'step' => null,
    'id' => null,
    'class' => '',
    'wrapperClass' => 'mb-4',
    'min' => null,
    'max' => null,
    'onchange' => null
])

@php
    $fieldId = $id ?? $name;
    $hasError = $errors->has($name);

    $baseClasses = match($type) {
        'select' => 'select select-bordered',
        'textarea' => 'textarea textarea-bordered',
        'checkbox' => 'checkbox',
        'radio' => 'radio',
        default => 'input input-bordered'
    };

    $errorClasses = match($type) {
        'select' => 'select-error',
        'textarea' => 'textarea-error',
        'checkbox', 'radio' => '',
        default => 'input-error'
    };

    $fieldClasses = $baseClasses . ($hasError ? ' ' . $errorClasses : '') . ($class ? ' ' . $class : '');
@endphp

<div class="form-control {{ $wrapperClass }}">
    @if($type !== 'checkbox' && $type !== 'radio')
        <label class="block text-sm font-semibold text-base-content mb-2" for="{{ $fieldId }}">
            {{ $label }}
            @if($optional)
                <span class="text-sm text-gray-500 ml-1">(Optional)</span>
            @endif
        </label>
    @endif

    @if($type === 'select')
        <select
            id="{{ $fieldId }}"
            name="{{ $name }}"
            class="{{ $fieldClasses }}"
            {{ $required ? 'required' : '' }}
            {{ $onchange ? 'onchange=' . $onchange : '' }}
            {{ $attributes->except(['name', 'label', 'type', 'value', 'placeholder', 'required', 'optional', 'options', 'rows', 'step', 'id', 'class', 'wrapperClass', 'min', 'max', 'onchange']) }}
        >
            @if($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            @foreach($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}>
                    {{ $optionLabel }}
                </option>
            @endforeach
        </select>

    @elseif($type === 'textarea')
        <textarea
            id="{{ $fieldId }}"
            name="{{ $name }}"
            class="{{ $fieldClasses }}"
            placeholder="{{ $placeholder }}"
            rows="{{ $rows }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->except(['name', 'label', 'type', 'value', 'placeholder', 'required', 'optional', 'options', 'rows', 'step', 'id', 'class', 'wrapperClass', 'min', 'max', 'onchange']) }}
        >{{ old($name, $value) }}</textarea>

    @elseif($type === 'checkbox')
        <div class="form-control">
            <label class="label cursor-pointer justify-start">
                <input
                    type="checkbox"
                    id="{{ $fieldId }}"
                    name="{{ $name }}"
                    value="{{ $value ?: '1' }}"
                    class="{{ $fieldClasses }}"
                    {{ old($name, $value) ? 'checked' : '' }}
                    {{ $required ? 'required' : '' }}
                    {{ $attributes->except(['name', 'label', 'type', 'value', 'placeholder', 'required', 'optional', 'options', 'rows', 'step', 'id', 'class', 'wrapperClass', 'min', 'max', 'onchange']) }}
                >
                <span class="label-text font-semibold ml-2">
                    {{ $label }}
                    @if($optional)
                        <span class="text-sm text-gray-500 ml-1">(Optional)</span>
                    @endif
                </span>
            </label>
        </div>

    @elseif($type === 'radio')
        @foreach($options as $optionValue => $optionLabel)
            <div class="form-control">
                <label class="label cursor-pointer justify-start">
                    <input
                        type="radio"
                        id="{{ $fieldId }}_{{ $loop->index }}"
                        name="{{ $name }}"
                        value="{{ $optionValue }}"
                        class="{{ $fieldClasses }}"
                        {{ old($name, $value) == $optionValue ? 'checked' : '' }}
                        {{ $required ? 'required' : '' }}
                        {{ $attributes->except(['name', 'label', 'type', 'value', 'placeholder', 'required', 'optional', 'options', 'rows', 'step', 'id', 'class', 'wrapperClass', 'min', 'max', 'onchange']) }}
                    >
                    <span class="label-text ml-2">{{ $optionLabel }}</span>
                </label>
            </div>
        @endforeach

    @else
        <input
            type="{{ $type }}"
            id="{{ $fieldId }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            class="{{ $fieldClasses }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $step ? 'step=' . $step : '' }}
            {{ $min ? 'min=' . $min : '' }}
            {{ $max ? 'max=' . $max : '' }}
            {{ $onchange ? 'onchange=' . $onchange : '' }}
            {{ $attributes->except(['name', 'label', 'type', 'value', 'placeholder', 'required', 'optional', 'options', 'rows', 'step', 'id', 'class', 'wrapperClass', 'min', 'max', 'onchange']) }}
        >
    @endif

    @error($name)
        <div class="label">
            <span class="label-text-alt text-error">{{ $message }}</span>
        </div>
    @enderror
</div>
