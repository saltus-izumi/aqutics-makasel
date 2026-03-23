<x-admin.auth-layout title="ENプロセス詳細">
    <div class="tw:w-full tw:h-full tw:overflow-auto">
        <div class="tw:px-[26px] tw:w-fit">
            <div class="tw:sticky tw:top-0 tw:bg-white">
                <livewire:admin.progress.en.detail-header :enProgress="$enProgress" />
            </div>
            <div class="tw:flex tw:gap-x-[78px]">
                <div class="tw:flex tw:flex-col tw:gap-y-[21px]">
                    <livewire:admin.progress.en.contract-terms :enProgress="$enProgress" />
                    <livewire:admin.progress.en.monthly-payment :enProgress="$enProgress" />
                    <livewire:admin.progress.en.initial-cost :enProgress="$enProgress" />
                    <livewire:admin.progress.en.broker :enProgress="$enProgress" />
                    <livewire:admin.progress.en.memo :enProgress="$enProgress" />
                </div>
                <div class="tw:flex tw:flex-col tw:gap-y-[21px]">
                    @if ($enProgress->applicant_type == $enProgress::APPLICANT_TYPE_INDIVIDUAL)
                        <livewire:admin.progress.en.individual-applicant :enProgress="$enProgress" />
                        <livewire:admin.progress.en.individual-occupant :enProgress="$enProgress" />
                        <livewire:admin.progress.en.individual-emergency-contact :enProgress="$enProgress" />
                    @else
                        <livewire:admin.progress.en.corporate-applicant :enProgress="$enProgress" />
                        <livewire:admin.progress.en.corporate-occupant :enProgress="$enProgress" />
                        <livewire:admin.progress.en.corporate-emergency-contact :enProgress="$enProgress" />
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin.auth-layout>
