@props([
    'title' => '',
    'subItems' => [],
])
<div class="tw:relative tw:h-full tw:flex tw:flex-col" x-data="{ open: false }"
        @mouseenter="open = true"
        @mouseleave="open = false"
>
    <div class="tw:flex tw:items-center tw:h-full">
        <div class="tw:text-[1.1rem] tw:px-[26px] tw:font-bold">
            {{ $title }}
        </div>
    </div>
    @if($subItems)
        <div class="tw:absolute tw:left-0 tw:top-full tw:z-20 tw:w-fit tw:bg-black/80 tw:px-[26px] tw:py-[15px] tw:flex tw:flex-col tw:gap-y-[15px]" x-show="open" x-cloak x-collapse>
            @foreach ($subItems as $url => $title)
                <div class="tw:text-nowrap tw:text-[1.1rem]" x-data="{ open: false }">
                    <a href="{{ $url }}">
                        {{ $title }}
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>
