
@props([
    'type' => 'button',
    'size' => ''
])
<x-button.button type="{{ $type }}" {{ $attributes->merge(['class' => 'tw:bg-[#000000] tw:hover:bg-[#555555] tw:text-white tw:border tw:border-[#aaaaaa]']) }} size="{{ $size }}">{{ $slot }}</x-button.button>
