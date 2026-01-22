@props([
    'title' => '',
    'open' => false,
    'event' => 'modal',
    'previewWidth' => null,
])

<div
    x-data="{ open: @js($open) }"
    x-on:open-{{ $event }}.window="open = true"
    x-on:close-{{ $event }}.window="open = false"
    x-cloak
>
    <template x-teleport="body">
        <div
            x-show="open"
            x-transition.opacity
            class="tw:fixed tw:inset-0 tw:z-[300] tw:flex tw:items-center tw:justify-center tw:bg-black/40 tw:px-[16px]"
            role="dialog"
            aria-modal="true"
            x-on:click.self="open = false"
        >
            <div
                x-show="open"
                x-transition
            class="tw:w-full tw:max-w-[900px] tw:rounded-[8px] tw:bg-white tw:shadow-lg"
            style="{{ $previewWidth ? "width: {$previewWidth}; max-width: {$previewWidth};" : '' }}"
            >
                <div class="tw:flex tw:items-center tw:justify-between tw:border-b tw:border-b-pm_gray_003 tw:px-[20px] tw:py-[12px]">
                    <div class="tw:text-[1.6rem] tw:font-bold">
                        {{ $title }}
                    </div>
                    <button
                        type="button"
                        class="tw:text-[1.6rem] tw:text-pm_gray_005"
                        x-on:click="open = false"
                        aria-label="閉じる"
                    >
                        ×
                    </button>
                </div>
                <div class="tw:px-[20px] tw:py-[16px]">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </template>
</div>
