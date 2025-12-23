@props([
    'name' => '',
    'value' => '',
    'checked' => null,
    'label' => '',
    'is_error' => false,
])
<div class="">
    <label class="tw:cursor-pointer tw:label">
        <input type="radio" name="{{ $name }}" value="{{ $value }}"
            {{ $attributes->merge(['class' => 'radio' . ($is_error ? ' tw:bg-red-100 ' : ''),]) }}
            {{ (string)$checked === (string)$value ? 'checked' : '' }} >
        <span class="ml-2">{{ $label }}</span>
    </label>
</div>
