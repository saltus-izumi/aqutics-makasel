
@props([
    'type' => 'button',
    'size' => ''
])
<x-button.button type="{{ $type }}" {{ $attributes->merge(['class' => 'tw:bg-[#b7b7b7] tw:text-white tw:hover:bg-gray-200 text-[#585d63]']) }} size="{{ $size }}">{{ $slot }}</x-button.button>
