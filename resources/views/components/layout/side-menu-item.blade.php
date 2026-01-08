@props([
    'title' => '',
])
<li x-data="{ open: false }">
    <div class="tw:text-[1.4rem] tw:leading-[42px]" @click="open = !open">
        <i class="tw:pr-3 tw:w-4 tw:inline-block tw:text-center" :class="open ? 'far fa-angle-down' : 'far fa-angle-right'"></i>{{ $title }}
    </div>
    <div class="tw:text-[1.2rem] tw:leading-[31px] tw:pl-[26px]" x-show="open" x-cloak x-collapse>
        {{ $slot }}
    </div>
</li>
