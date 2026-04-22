@props([
    'type' => 'text',
    'name' => '',
    'palceholder' => '',
    'errors',
    'border' => true,
])
<textarea type="{{ $type }}" name="{{ $name }}"
    @class([
        'tw:border tw:border-gray-300' => $border,
        'tw:p-1 tw:w-full tw:bg-white',
        'tw:bg-white' => !$attributes->has('class') || !str_contains($attributes->get('class'), 'tw:bg-'),
        'tw:read-only:bg-gray-100',
        $attributes->get('class'),
        'tw:bg-red-100' => $errors->has($name),
    ])
    {{ $attributes->except('class') }}
    placeholder="{{ $palceholder }}">{{ $slot }}</textarea>
