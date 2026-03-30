<div
    class="tw:w-[806px]"
    x-data="geProgressStep7"
    @multi-file-upload2:selected.window="handleSelect($event)"
    @multi-file-upload2:removed.window="handleRemove($event)"
>
    <div class="tw:w-full tw:pl-1 tw:bg-[#f3f3f3] tw:text-[1.1rem]">
        STEP７（原復発注）
    </div>
    <div class="tw:w-full tw:mt-[42px] tw:px-[26px]">
        <div class="tw:w-full">
            <x-form.multi_file_upload2
                name="purchase_order"
                title="発注書"
                instanceId="ge-progress-purchase-order-{{ $geProgress->id }}"
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
        <div class="tw:mt-[21px]">
            実行担当 ⇒ 原復会社<br>
            <x-form.textarea class="tw:!h-[105px]" placeholder="引継ぎコメント" wire:model.live="executorToRestorationCompanyMessage"></x-form.textarea>
        </div>
        <div class="tw:h-[42px] tw:mt-[26px] tw:flex tw:justify-end tw:items-center tw:gap-x-[26px]">
            <div>
                <x-button.blue class="tw:!h-[31px] tw:!rounded-lg tw:text-[1.2rem]">原復会社発注</x-button.blue>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('geProgressStep7', () => ({
                instanceMap: [
                    {
                        instanceId: @js('ge-progress-purchase-order-' . $geProgress->id),
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
