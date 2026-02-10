<div
    class="tw:w-[832px]"
    x-data="geProgressStep4"
    @multi-file-upload2:selected.window="handleSelect($event)"
    @multi-file-upload2:removed.window="handleRemove($event)"
>
    <div class="tw:w-full tw:pl-1 tw:bg-[#f3f3f3] tw:text-[1.1rem]">
        STEP４（実行担当＿提案準備）
    </div>
    <div class="tw:w-full tw:mt-[21px] tw:px-[26px]">
        <table class="tw:w-full tw:mt-[42px] tw:table-fixed">
            <tr class="tw:h-[42px]">
                <td class="tw:w-[130px] tw:text-[1.4rem] tw:text-center tw:bg-[#efefef] tw:border tw:border-[#cccccc]">下代</td>
                <td class="tw:border tw:border-[#cccccc]">
                    <x-form.input-number name="securityDepositAmount" class="tw:text-center tw:font-bold tw:text-[1.7rem]" :border="false" wire:model.live="costAmount" />
                </td>
                <td class="tw:w-[130px] tw:text-[1.4rem] tw:text-center tw:bg-[#efefef] tw:border tw:border-[#cccccc]">上代</td>
                <td class="tw:border tw:border-[#cccccc]">
                    <x-form.input-number name="proratedRentAmount" class="tw:text-center tw:font-bold tw:text-[1.7rem]" :border="false" wire:model.live="chargeAmount" />
                </td>
            </tr>
            <tr class="tw:h-[42px]">
                <td class="tw:w-[130px] tw:text-[1.4rem] tw:text-center tw:bg-[#efefef] tw:border tw:border-[#cccccc]">利益</td>
                <td class="tw:text-center tw:font-bold tw:text-[1.7rem] tw:border tw:border-[#cccccc]">
                    {{ $profitAmount }}
                </td>
                <td class="tw:w-[130px] tw:text-[1.4rem] tw:text-center tw:text-white tw:bg-black tw:border tw:border-[#cccccc]">利益率</td>
                <td @class([
                    'tw:text-center tw:font-bold tw:text-[1.7rem] tw:border tw:border-[#cccccc]',
                    'tw:text-red-600' => $profitRate < 30,
                ])>
                    @if ($profitRate < 30)×@endif
                    {{ $profitRate }}%
                </td>
            </tr>
        </table>
        <div class="tw:mt-[21px] tw:flex tw:gap-x-[26px]">
            <div class="tw:w-[130px]">
                <div class="tw:h-[21px] tw:text-[#d9d9d9]">
                    ※退去精算連動
                </div>
                <div class="tw:bg-[#f3f3f3] tw:pl-1">
                    <div class="tw:w-full tw:h-[21px] tw:leading-[21px] tw:text-[#4a86e8]">
                        退去時清算書
                    </div>
                    <div class="tw:w-full tw:h-[21px] tw:leading-[21px] tw:text-[#4a86e8]">
                        下代見積もり
                    </div>
                </div>
            </div>
            <div class="tw:w-[130px]">
                <div class="tw:h-[21px] tw:text-[#d9d9d9]">
                    ※退去精算連動
                </div>
                <div class="tw:bg-[#f3f3f3] tw:pl-1">
                    <div class="tw:w-full tw:h-[42px] tw:leading-[42px] tw:text-[#4a86e8]">
                        立会写真
                    </div>
                </div>
            </div>
            <div class="tw:w-[234px]">
                <div class="tw:h-[21px] tw:text-[#d9d9d9]">
                    ※オーナー添付選択
                </div>
                <div class="tw:w-full">
                    <x-form.multi_file_upload2
                        name="sales_estimate"
                        title="上代見積もり"
                        instanceId="ge-progress-sales-estimate-{{ $progress->id }}"
                        class="tw:h-[42px]"
                        maxFileCount="20"
                        maxFileSize="25MB"
                        :allowMimeTypes="[
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/webp',
                            'image/bmp',
                            'image/tiff',
                            'image/heic',
                            'image/heif',
                            'application/pdf',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        ]"
                        :files="$salesEstimateFiles"
                    />
                </div>
            </div>
            <div class="tw:h-[42px] tw:pt-[21px] tw:flex tw:gap-x-[26px] tw:items-start">
                <x-button.gray class="tw:!w-[216px] tw:!h-[45px] tw:!px-[15px] tw:!text-black tw:!text-[1.2rem] tw:!rounded-lg">Before 写真編集</x-button.gray>
            </div>
        </div>
        <div class="tw:mt-[21px]">
            実行担当 ⇒ 責任担当<br>
            <x-form.textarea class="tw:!h-[105px]" placeholder="引継ぎコメント" wire:model.live="responsiblePersonMessage"></x-form.textarea>
        </div>
        <div class="tw:h-[42px] tw:mt-[26px] tw:flex tw:justify-end tw:items-center tw:gap-x-[26px]">
            <div>
                <x-button.blue class="tw:!h-[31px] tw:!rounded-lg tw:text-[1.2rem]">実行担当判断完了</x-button.blue>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('geProgressStep4', () => ({
                instanceMap: [
                    {
                        instanceId: @js('ge-progress-sales-estimate-' . $progress->id),
                        uploadProperty: 'salesEstimateUploads',
                        saveMethod: 'saveSalesEstimateUploads',
                        removeMethod: 'removeSalesEstimateFile',
                    },
                ],
                handleSelect(event) {
                    const target = this.instanceMap.find((item) => item.instanceId === event?.detail?.instanceId);
                    if (!target) {
                        return;
                    }
                    const files = event.detail?.files || [];
                    if (files.length === 0) {
                        return;
                    }
                    this.$wire.uploadMultiple(target.uploadProperty, files, () => {
                        this.$wire.call(target.saveMethod);
                    });
                },
                handleRemove(event) {
                    const target = this.instanceMap.find((item) => item.instanceId === event?.detail?.instanceId);
                    if (!target) {
                        return;
                    }
                    const fileId = event?.detail?.file?.id || null;
                    this.$wire.call(target.removeMethod, fileId);
                },
            }));
        });
    </script>
@endpush
