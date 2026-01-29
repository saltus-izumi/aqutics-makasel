@props([
    'text' => '',
])

<span
    {{ $attributes->merge(['class' => 'tw:w-full tw:h-full tw:relative tw:inline-flex tw:items-center tw:justify-center']) }}
    x-data="{ open: false }"
    x-on:mouseenter="open = true"
    x-on:mouseleave="open = false"
>
    <span class="tw:relative tw:z-0">{{ $slot }}</span>
    @if (filled($text))
        <span
            class="tw:absolute tw:z-50 tw:bottom-full tw:left-1/2 tw:-translate-x-1/2 tw:mb-1 tw:whitespace-nowrap tw:rounded tw:bg-[#333] tw:text-white tw:text-[0.9rem] tw:px-2 tw:py-1 tw:pointer-events-none"
            x-show="open"
            x-transition.opacity.duration.150ms
            x-cloak
        >{{ $text }}</span>
    @endif
</span>
