
@props([
    'type' => 'button',
    'size' => ''
])
<x-button.button type="{{ $type }}" {{ $attributes->merge(['class' => 'tw:bg-[#efefef] tw:hover:bg-gray-100 text-[#585d63]']) }} size="{{ $size }}">{{ $slot }}</x-button.button>
