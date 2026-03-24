<x-admin.auth-layout title="ENプロセス詳細">
    <div class="tw:w-full tw:h-full tw:overflow-auto">
        <div class="tw:px-[26px] tw:w-fit">
            <div class="tw:sticky tw:top-0 tw:bg-white">
                <livewire:admin.progress.en.detail-header :enProgress="$enProgress" mode="approval" />
            </div>
            <div class="tw:pt-[42px] tw:flex tw:flex-col tw:gap-y-[21px]">
                <livewire:admin.progress.en.guarantee-company-screening :enProgress="$enProgress" />
                <livewire:admin.progress.en.wp-screening :enProgress="$enProgress" />
                <livewire:admin.progress.en.owner-approved :enProgress="$enProgress" />
                <livewire:admin.progress.en.initial-payment :enProgress="$enProgress" />
            </div>
        </div>
    </div>
</x-admin.auth-layout>
