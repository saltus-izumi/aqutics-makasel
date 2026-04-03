<x-admin.auth-layout title="TEプロセス詳細">
    <div class="tw:w-full tw:h-full tw:overflow-auto">
        <div class="tw:px-[26px] tw:w-fit">
            <div class="tw:sticky tw:top-0 tw:bg-white">
                <livewire:admin.progress.te.detail-header :teProgress="$teProgress" />
            </div>
            <div class="tw:flex tw:gap-x-[78px]">
                <div class="tw:mt-[42px]">
                    <div>
                        <livewire:admin.progress.te.step1 :teProgress="$teProgress" />
                    </div>
                    <div class="tw:mt-[21px]">
                        <livewire:admin.progress.te.step2 :teProgress="$teProgress" />
                    </div>
                </div>
                <div class="tw:mt-[42px]">
                    <div>
                    </div>
                    <div class="tw:mt-[21px] tw:pb-[21px]">
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin.auth-layout>
