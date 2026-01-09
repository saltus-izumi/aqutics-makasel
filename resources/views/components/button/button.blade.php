
@props([
    'type' => 'button',
    'size' => ''
])
@php
    switch ($size) {
        case 'sm':
            $class = 'tw:px-[8px] tw:py-[2px] tw:min-w-[50px] tw:rounded-md ';
            break;
        default:
            $class = 'tw:px-[30px] tw:h-[42px] tw:min-w-[100px] tw:rounded-sm tw-text-center tw-border-none tw-outline-none ';
    }
@endphp
<button type="{{ $type }}" {{ $attributes->merge(['class' => $class . ' tw-text-center tw-border-none tw-outline-none tw-bg-inherit disabled:tw-bg-gray-300 tw:cursor-pointer']) }}>{{ $slot }}</button>
{{-- <button type="{{ $type }}" {{ $attributes->merge(['class' => ' tw:bg-gray-100 tw:hover:bg-gray-200 text-[#585d63] tw:text-center tw:border tw:border-gray-600 tw:outline-none tw:cursor-pointer']) }}>{{ $slot }}</button> --}}
