@props([
    'name' => '',
    'palceholder' => '',
    'is_error' => false,
])
<input type="file" name="{{ $name }}"
    @class([
        'tw:border tw:border-gray-300 tw:placeholder:text-gray-400  tw:w-full tw:rounded-md',
        'tw:file:bg-gray-200 tw:file:border-r tw:file:border-gray-300 tw:file:text-gray-700 tw:file:px-4 tw:file:py-2 tw:file:cursor-pointer tw:file:hover:bg-gray-400',
        'tw:bg-white' => !$attributes->has('class') || !str_contains($attributes->get('class'), 'tw:bg-'),
        $attributes->get('class'),
        'tw:bg-red-100' => $is_error,
    ])
    {{ $attributes->except('class') }}
    placeholder="{{ $palceholder }}">
