<div
    class="tw:w-[832px]"
    x-data="teProgressHardStep4"
    @multi-file-upload2:selected.window="handleSelect($event)"
    @multi-file-upload2:removed.window="handleRemove($event)"
>
    <div class="tw:w-full tw:pl-1 tw:bg-[#f3f3f3] tw:text-[1.1rem]">
        ハード｜STEP４（実行担当＿修繕発注）
    </div>
    <div class="tw:w-full tw:px-[26px]">
        <div class="tw:mt-[21px]">
            <div class="tw:flex tw:justify-between tw:gap-x-[26px]">
                <div class="tw:w-[234px]">
                    <div class="tw:w-full">
                        <x-form.multi_file_upload2
                            name="purchase_order"
                            title="発注書"
                            instanceId="te-progress-purchase-order-{{ $teProgress->id }}"
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
                            :files="$purchaseOrderFiles"
                        />
                    </div>
                </div>
                <div class="">
                    <table class="tw:table-fixed">
                        <tr class="tw:h-[42px]">
                            <td class="tw:w-[130px] tw:text-[1.3rem] tw:text-center tw:text-white tw:font-bold tw:bg-black tw:border tw:border-[#cccccc]">発注日</td>
                            <td class="tw:w-[260px] tw:border tw:border-[#cccccc]">
                                <x-form.input-date name="pcHachuDate" class="tw:text-center tw:pr-[20px] tw:font-bold tw:text-[1.7rem]" :border="false" :is_error="$errors->has('pcHachuDate')" wire:model.live="pcHachuDate" />
                            </td>
                        </tr>
                    </table>
                    @error('pcHachuDate')
                        <div class="tw:mt-[6px] tw:text-[1.2rem] tw:text-[#ff0000]">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('teProgressHardStep4', () => ({
                instanceMap: [
                    {
                        instanceId: @js('te-progress-purchase-order-' . $teProgress->id),
                        uploadProperty: 'purchaseOrderUploads',
                        saveMethod: 'savePurchaseOrderUploads',
                        removeMethod: 'removePurchaseOrderFile',
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
