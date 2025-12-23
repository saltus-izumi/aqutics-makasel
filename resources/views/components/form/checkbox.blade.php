@props([
    'name' => '',
    'value' => '',
    'checked' => false,
    'label' => '',
    'is_error' => false,
    'label_class' => ''
])
<label class="tw:cursor-pointer tw:label">
    <input type="checkbox" name="{{ $name }}" value="{{ $value }}" {{ $attributes->merge(['class' => 'tw:checkbox tw:checkbox-sm tw:checkbox-primary tw:border-1 tw:border-solid  ' . ($is_error ? ' tw:bg-red-100 ' : '')]) }} {{ (string)$checked === (string)$value ? 'checked' : '' }} >
    <span class="tw:ml-2 tw:font-normal {{ $label_class }}">{{ $label }}</span>
</label>
