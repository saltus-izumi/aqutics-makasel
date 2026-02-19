<div class="tw:w-[806px]">
    <div class="tw:w-full tw:pl-1 tw:bg-[#f3f3f3] tw:text-[1.1rem]">
        STEP８（完了報告）
    </div>
    <div class="tw:w-full tw:mt-[21px] tw:px-[26px]">
        <div class="tw:h-[84px] tw:flex tw:gap-x-[78px] tw:items-start">
            <div>
                <div class="tw:h-[21px] tw:text-[0.9rem] tw:text-[#999999]">※オーナー添付選択</div>
                <div class="tw:h-[42px] tw:flex tw:items-start">
                    <x-button.gray class="tw:!w-[208px] tw:!h-[45px] tw:!px-[15px] tw:!text-black tw:!text-[1.2rem] tw:!rounded-lg">Before  & After写真編集</x-button.gray>
                </div>
            </div>
            <div class="tw:h-full tw:flex tw:items-center">
                @if ($geProgress?->completion_report_operation_id)
                    <a href="{{ route('admin.operation.edit', [
                        'operationId' => $geProgress?->completion_report_operation_id,
                    ]) }}">
                        <x-button.blue class="tw:!h-[31px] tw:!rounded-lg tw:text-[1.2rem]">オペレーション編集</x-button.blue>
                    </a>
                @else
                    <a href="{{ route('admin.operation.create.ge', [
                        'geProgressId' => $geProgress?->id,
                        'geProgressStep' => 'completion_report',
                    ]) }}">
                        <x-button.blue class="tw:!h-[31px] tw:!rounded-lg tw:text-[1.2rem]">オペレーション作成</x-button.blue>
                    </a>
                @endif

            </div>
        </div>
    </div>
</div>
