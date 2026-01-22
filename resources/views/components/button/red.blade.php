
@props([
    'type' => 'button',
    'size' => ''
])
<x-button.button type="{{ $type }}" {{ $attributes->merge(['class' => 'tw:bg-[#ff0000] tw:hover:bg-[#ff5555] tw:text-white tw:border tw:border-[#aa0000]']) }} size="{{ $size }}">{{ $slot }}</x-button.button>
