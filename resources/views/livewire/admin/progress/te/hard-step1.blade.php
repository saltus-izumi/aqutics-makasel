<div
    class="tw:w-[806px]"
    x-data="teProgressHardStep1"
    @multi-file-upload2:selected.window="handleSelect($event)"
    @multi-file-upload2:removed.window="handleRemove($event)"
>
    <div class="tw:w-full tw:pl-1 tw:bg-[#f3f3f3] tw:text-[1.1rem]">
        ハード｜STEP１（実行担当＿提案準備）
    </div>
    <div class="tw:w-full tw:px-[26px]">

        <table class="tw:w-full tw:mt-[42px] tw:table-fixed">
            <tr class="tw:h-[42px]">
                <td class="tw:w-[130px] tw:text-[1.4rem] tw:text-center tw:bg-[#efefef] tw:border tw:border-[#cccccc]">下代</td>
                <td class="tw:border tw:border-[#cccccc]">
                    <x-form.input-number name="costAmount" class="tw:text-right tw:pr-[20px] tw:font-bold tw:text-[1.7rem]" :border="false" wire:model.live="costAmount" />
                </td>
                <td class="tw:w-[130px] tw:text-[1.4rem] tw:text-center tw:bg-[#efefef] tw:border tw:border-[#cccccc]">上代</td>
                <td class="tw:border tw:border-[#cccccc]">
                    <x-form.input-number name="chargeAmount" class="tw:text-right tw:pr-[20px] tw:font-bold tw:text-[1.7rem]" :border="false" wire:model.live="chargeAmount" />
                </td>
            </tr>
            <tr class="tw:h-[42px]">
                <td class="tw:w-[130px] tw:text-[1.4rem] tw:text-center tw:bg-[#efefef] tw:border tw:border-[#cccccc]">利益</td>
                <td class="tw:text-right tw:pr-[20px] tw:font-bold tw:text-[1.7rem] tw:border tw:border-[#cccccc]">
                    {{ $profitAmount }}
                </td>
                <td class="tw:w-[130px] tw:text-[1.4rem] tw:text-center tw:text-white tw:bg-black tw:border tw:border-[#cccccc]">利益率</td>
                <td @class([
                    'tw:text-right tw:pr-[20px] tw:text-center tw:font-bold tw:text-[1.7rem] tw:border tw:border-[#cccccc]',
                    'tw:text-red-600' => $profitRate < 30,
                ])>
                    @if ($profitRate < 30)<span class="tw:pr-3">×</span>@endif
                    {{ $profitRate }}%
                </td>
            </tr>
        </table>

        <div class="tw:mt-[21px]">
            <div class="tw:h-[21px] tw:text-[0.9rem] tw:text-[#999999]">
                添付ファイル・画像、ＰＤＦ、Excel、Wordファイルが送信可能です。（可能ファイル数：20個／1ファイルの最大サイズ：25MB）
            </div>
            <div class="tw:flex tw:gap-x-[26px]">
                <div class="tw:flex-1">
                    <div class="tw:w-full">
                        <x-form.multi_file_upload2
                            name="on_site_inspection_report"
                            title="現調報告書"
                            instanceId="te-progress-on-site-inspection-report-{{ $teProgress->id }}"
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
                            :files="$onSiteInspectionReportFiles"
                        />
                    </div>
                </div>
                <div class="tw:flex-1">
                    <div class="tw:w-full">
                        <x-form.multi_file_upload2
                            name="lower_estimate"
                            title="下代見積もり"
                            instanceId="te-progress-lower-estimate-{{ $teProgress->id }}"
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
                            :files="$lowerEstimateFiles"
                        />
                    </div>
                </div>
                <div class="tw:flex-1">
                    <div class="tw:w-full">
                        <x-form.multi_file_upload2
                            name="retail_estimate"
                            title="上代見積もり"
                            instanceId="te-progress-retail-estimate-{{ $teProgress->id }}"
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
                            :files="$retailEstimateFiles"
                        />
                    </div>
                </div>
            </div>
        </div>
        <div class="tw:mt-[21px]">
            実行担当 ⇒ 責任担当<br>
            <x-form.textarea class="tw:!h-[105px]" placeholder="実行担当 ⇒ 責任担当" wire:model.live="executorToResponsibleMessage"></x-form.textarea>
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
            Alpine.data('teProgressHardStep1', () => ({
                instanceMap: [
                    {
                        instanceId: @js('te-progress-on-site-inspection-report-' . $teProgress->id),
                        uploadProperty: 'onSiteInspectionReportUploads',
                        saveMethod: 'saveOnSiteInspectionReportUploads',
                        removeMethod: 'removeOnSiteInspectionReportFile',
                    },
                    {
                        instanceId: @js('te-progress-lower-estimate-' . $teProgress->id),
                        uploadProperty: 'lowerEstimateUploads',
                        saveMethod: 'saveLowerEstimateUploads',
                        removeMethod: 'removeLowerEstimateFile',
                    },
                    {
                        instanceId: @js('te-progress-retail-estimate-' . $teProgress->id),
                        uploadProperty: 'retailEstimateUploads',
                        saveMethod: 'saveRetailEstimateUploads',
                        removeMethod: 'removeRetailEstimateFile',
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
