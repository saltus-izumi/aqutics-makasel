@props([
    'title' => '並び順 / ID絞り込み',
    'xTitle' => null,
    'sortLabel' => '並び順',
    'label' => '値入力',
    'blankLabel' => '空白',
    'notBlankLabel' => '空白以外',
    'placeholder' => 'IDで絞り込み',
    'ascLabel' => '昇順',
    'descLabel' => '降順',
    'applyLabel' => '適用',
    'resetLabel' => 'クリア',
    'sortModel' => 'sortOrder',
    'filterModel' => 'filterId',
    'blankModel' => 'filterBlank',
    'onApply' => 'applySortFilter()',
    'onReset' => 'resetSortFilter()',
])

<div
    {{ $attributes->merge(['class' => 'tw:fixed tw:z-50 tw:w-[260px] tw:rounded tw:border tw:border-gray-200 tw:bg-white tw:shadow-lg tw:p-3']) }}
    x-transition:enter="tw:transition tw:ease-out tw:duration-100"
    x-transition:enter-start="tw:opacity-0 tw:scale-95"
    x-transition:enter-end="tw:opacity-100 tw:scale-100"
    x-transition:leave="tw:transition tw:ease-in tw:duration-75"
    x-transition:leave-start="tw:opacity-100 tw:scale-100"
    x-transition:leave-end="tw:opacity-0 tw:scale-95"
    x-on:click.stop
    x-cloak
>
    <div
        class="tw:text-sm tw:font-semibold tw:text-gray-800 tw:mb-2"
        @if($xTitle) x-text="{{ $xTitle }}" @endif
    >{{ $title }}</div>
    <div class="tw:space-y-2">
        <div>
            <div class="tw:text-xs tw:text-gray-600 tw:mb-1">{{ $sortLabel }}</div>
            <div class="tw:flex tw:gap-2">
                <label class="tw:inline-flex tw:items-center tw:gap-1">
                    <input type="radio" class="tw:accent-blue-600" value="asc" x-model="{{ $sortModel }}">
                    <span class="tw:text-sm">{{ $ascLabel }}</span>
                </label>
                <label class="tw:inline-flex tw:items-center tw:gap-1">
                    <input type="radio" class="tw:accent-blue-600" value="desc" x-model="{{ $sortModel }}">
                    <span class="tw:text-sm">{{ $descLabel }}</span>
                </label>
            </div>
        </div>
        <div>
            <div class="tw:text-xs tw:text-gray-600 tw:mb-1">{{ $label }}</div>
            <div class="tw:flex tw:gap-3 tw:mb-2">
                <label class="tw:inline-flex tw:items-center tw:gap-1">
                    <input type="radio" class="tw:accent-blue-600" value="blank" x-model="{{ $blankModel }}" x-on:change="handleFilterBlankChange($event)">
                    <span class="tw:text-sm">{{ $blankLabel }}</span>
                </label>
                <label class="tw:inline-flex tw:items-center tw:gap-1">
                    <input type="radio" class="tw:accent-blue-600" value="not_blank" x-model="{{ $blankModel }}">
                    <span class="tw:text-sm">{{ $notBlankLabel }}</span>
                </label>
            </div>
            <input
                type="text"
                class="tw:w-full tw:border tw:border-gray-300 tw:rounded tw:px-2 tw:py-1 tw:text-sm"
                placeholder="{{ $placeholder }}"
                x-model="{{ $filterModel }}"
                x-on:input="handleFilterInput($event)"
                x-on:keydown.enter.prevent="{{ $onApply }}"
            >
        </div>
        <div class="tw:flex tw:justify-end tw:gap-2">
            <button type="button" class="tw:text-sm tw:px-2 tw:py-1 tw:border tw:rounded" x-on:click="{{ $onReset }}">{{ $resetLabel }}</button>
            <button type="button" class="tw:text-sm tw:px-2 tw:py-1 tw:bg-blue-600 tw:text-white tw:rounded" x-on:click="{{ $onApply }}">{{ $applyLabel }}</button>
        </div>
    </div>
</div>
