@props([
    'type' => 'text',
    'name' => '',
    'palceholder' => '',
    'is_error' => false,
    'border' => true,
    'unit' => '',
])
<div
    @class([
        'tw:border tw:border-gray-300' => $border,
        'tw:w-full tw:rounded-md tw:flex tw:items-center',
        'tw:bg-white' => !$attributes->has('class') || !str_contains($attributes->get('class'), 'tw:bg-'),
        $attributes->get('class'),
        'tw:bg-red-100' => $is_error,

    ])
>
    <input type="{{ $type }}" name="{{ $name }}"
        @class([
            'tw:placeholder:text-gray-400 tw:py-2 tw:px-3 tw:mr-2 tw:rounded-md tw:w-[calc(100%-2rem)]',
            'tw:bg-white' => !$attributes->has('textClass') || !str_contains($attributes->get('textClass'), 'tw:bg-'),
            'tw:read-only:bg-gray-100',
            $attributes->get('textClass'),
            'tw:bg-red-100' => $is_error,
            
        ])
        {{ $attributes->except(['class', 'textClass']) }}
        placeholder="{{ $palceholder }}">
    {{ $unit}}
</div>
