
@props([
    'type' => 'button',
    'size' => ''
])
<x-button.button type="{{ $type }}" {{ $attributes->merge(['class' => 'tw:bg-gray-100 tw:hover:bg-gray-200 text-[#585d63] tw:border tw:border-gray-600']) }} size="{{ $size }}">{{ $slot }}</x-button.button>
