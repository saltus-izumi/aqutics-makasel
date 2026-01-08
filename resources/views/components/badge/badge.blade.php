@props([
    'size' => '',
    'class' => '',
])
<span {{ $attributes->merge(['class' => $class . ' tw:text-[1rem] tw:px-4 tw:py-0.5 tw:rounded-[4px] tw:text-white']) }}>{{ $slot }}</span>
