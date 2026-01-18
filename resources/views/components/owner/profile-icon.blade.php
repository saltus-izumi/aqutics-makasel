<div class="tw:w-full tw:h-full tw:relative">
    <div class="tw:inline-flex tw:items-center tw:justify-center tw:rounded-full tw:w-full tw:h-full tw:text-white tw:font-medium tw:select-none" style="background-color: #{{ $bgColor }};">
        @if ($imageDataUrl)
            <img src="{{ $imageDataUrl }}" class="tw:w-full tw:h-full tw:object-cover tw:rounded-full">
        @else
            {{ $initial }}
        @endif
    </div>
</div>
