
@props([
    'type' => 'button',
    'size' => ''
])
<x-button.button type="{{ $type }}" {{ $attributes->merge(['class' => 'tw:bg-[#cfe2f3] tw:hover:bg-[#c2ddf5] tw:text-black tw:border tw:border-blue-600']) }} size="{{ $size }}">{{ $slot }}</x-button.button>
