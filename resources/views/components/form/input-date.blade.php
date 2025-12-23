@props([
    'name' => 'date',
    'value' => '',
    'placeholder' => '',
    'is_error' => false,
])

<input
    x-data
    x-init="flatpickr($el, { dateFormat: 'Y/m/d', defaultDate: '{{ $value }}', allowInput: true })"
    type="text"
    name="{{ $name }}"
    placeholder="{{ $placeholder }}"
    {{ $attributes->merge(['class' => 'tw:input tw:input-bordered tw:h-[1.8em] tw:px-[4px] tw:w-full tw:bg-white ' . ($is_error ? ' !tw:bg-red-100 ' : '')]) }}
>
