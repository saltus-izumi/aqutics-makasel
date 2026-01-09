
@props([
    'type' => 'button',
    'size' => ''
])
<x-button.button type="{{ $type }}" {{ $attributes->merge(['class' => 'tw:bg-[#4a86e8] tw:hover:bg-blue-500 tw:text-white tw:border tw:border-blue-600']) }} size="{{ $size }}">{{ $slot }}</x-button.button>
