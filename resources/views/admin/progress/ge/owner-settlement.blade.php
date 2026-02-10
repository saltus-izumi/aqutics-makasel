<x-admin.auth-layout title="原復プロセス詳細">
    <div class="tw:w-full tw:h-full tw:overflow-auto">
        <div class="tw:px-[26px] tw:w-fit">
            <div class="tw:sticky tw:top-0 tw:bg-white">
                <livewire:admin.progress.ge.detail-header :progress="$progress" mode="owner-settlement" />
            </div>
            <div class="tw:flex tw:gap-x-[78px]">
                <div class="tw:mt-[42px]">
                    <div>
                        <livewire:admin.progress.ge.step4 :progress="$progress" />
                    </div>
                    <div class="tw:mt-[21px] tw:pb-[21px]">
                        <livewire:admin.progress.ge.step5 :progress="$progress" />
                    </div>
                </div>
                <div class="tw:mt-[42px]">
                    <div>
                        <livewire:admin.progress.ge.step6 :progress="$progress" />
                    </div>
                    <div class="tw:mt-[21px]">
                        <livewire:admin.progress.ge.step7 :progress="$progress" />
                    </div>
                    <div class="tw:mt-[21px]">
                        <livewire:admin.progress.ge.step8 :progress="$progress" />
                    </div>
                    <div class="tw:mt-[21px] tw:pb-[21px]">
                        <livewire:admin.progress.ge.memo :progress="$progress" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin.auth-layout>
