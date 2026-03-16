<x-admin.auth-layout title="ENプロセス詳細">
    <div class="tw:w-full tw:h-full tw:overflow-auto">
        <div class="tw:px-[26px] tw:w-fit">
            <div class="tw:sticky tw:top-0 tw:bg-white">
                <livewire:admin.progress.en.detail-header :enProgress="$enProgress" />
            </div>
            <div class="tw:flex tw:gap-x-[78px]">
                <div class="tw:flex tw:flex-col tw:gap-y-[21px] tw:mt-[42px]">
                    <livewire:admin.progress.en.contract-terms :enProgress="$enProgress" />
                    <livewire:admin.progress.en.monthly-payment :enProgress="$enProgress" />
                    <livewire:admin.progress.en.initial-cost :enProgress="$enProgress" />
                    <livewire:admin.progress.en.broker :enProgress="$enProgress" />
                    <livewire:admin.progress.en.memo :enProgress="$enProgress" />
                </div>
                <div class="tw:mt-[42px]">
                    <div>
                        <livewire:admin.progress.ge.step2 :geProgress="$enProgress" />
                    </div>
                    <div class="tw:mt-[21px] tw:pb-[21px]">
                        <livewire:admin.progress.ge.step3 :geProgress="$enProgress" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin.auth-layout>
