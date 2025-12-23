@props([
    'type' => 'text',
    'name' => '',
    'palceholder' => '',
    'after_label' => '',
    'is_error' => false,
])
<input type="{{ $type }}" name="{{ $name }}"
    @class([
        'tw:input tw:input-bordered tw:h-[1.8em] tw:px-[4px] tw:w-full',
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
