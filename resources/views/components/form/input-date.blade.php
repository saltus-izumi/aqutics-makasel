@props([
    'name' => 'date',
    'value' => '',
    'placeholder' => '',
    'is_error' => false,
    'border' => true,
])

<input
    x-data
    x-init="flatpickr($el, { dateFormat: 'Y/m/d', defaultDate: '{{ $value }}', allowInput: true })"
    type="text"
    name="{{ $name }}"
    placeholder="{{ $placeholder }}"
    @class([
        'tw:border tw:border-gray-300' => $border,
        'tw:placeholder:text-gray-400 tw:py-2 tw:px-3  tw:w-full tw:rounded-md',
        'tw:bg-white' => !$attributes->has('class') || !str_contains($attributes->get('class'), 'tw:bg-'),
        $attributes->get('class'),
        'tw:bg-red-100' => $is_error,
    ])
    {{ $attributes->except('class') }}
>
