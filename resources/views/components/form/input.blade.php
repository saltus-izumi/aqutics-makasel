@props([
    'type' => 'text',
    'name' => '',
    'palceholder' => '',
    'after_label' => '',
    'is_error' => false,
])
<input type="{{ $type }}" name="{{ $name }}"
    @class([
        'tw:border tw:border-gray-300 tw:placeholder:text-gray-400 tw:py-2 tw:px-3 tw:w-full tw:rounded-md',
        'tw:bg-white' => !$attributes->has('class') || !str_contains($attributes->get('class'), 'tw:bg-'),
        'tw:read-only:bg-gray-100',
        'tw:inline-block' => $after_label,
        $attributes->get('class'),
        'tw:bg-red-100' => $is_error,
    ])
    {{ $attributes->except('class') }}
    placeholder="{{ $palceholder }}">
@if ($after_label)
    {{ $after_label}}
@endif
