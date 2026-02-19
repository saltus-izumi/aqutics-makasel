<div
    class="tw:w-[806px]"
    x-data="geProgressStep3"
    @multi-file-upload2:selected.window="handleSelect($event)"
    @multi-file-upload2:removed.window="handleRemove($event)"
>
    <div class="tw:w-full tw:pl-1 tw:bg-[#f3f3f3] tw:text-[1.1rem]">
        STEP３（工事完了報告）
    </div>
    <div class="tw:w-full tw:mt-[21px] tw:px-[26px]">
        <div class="tw:h-[21px] tw:text-[0.9rem] tw:text-[#999999]">※オーナー添付選択</div>
        <div class="tw:h-[63px] tw:flex tw:gap-x-[26px] tw:items-start">
            <x-button.gray class="tw:!w-[260px] tw:!h-[45px] tw:!px-[15px] tw:!text-black tw:!text-[1.2rem] tw:!rounded-lg">完工写真編集</x-button.gray>
        </div>
        <div class="tw:mb-[21px]">
            <div class="tw:text-[0.9rem] tw:text-[#999999]">
                添付ファイル・画像、ＰＤＦ、Excel、Wordファイルが送信可能です。（可能ファイル数：20個／1ファイルの最大サイズ：25MB）
            </div>
            <div class="tw:w-full">
                <x-form.multi_file_upload2
                    name="completion_photo"
                    title="その他完工写真"
                    instanceId="ge-progress-completion-photo-{{ $geProgress->id }}"
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
                    :files="$completionPhotoFiles"
                />
            </div>
        </div>
        <div class="tw:mt-[21px]">
            完工メッセージ<br>
            <x-form.textarea class="tw:!h-[105px]" placeholder="引継ぎコメント" wire:model.live="completionMessage"></x-form.textarea>
        </div>
        <div class="tw:h-[42px] tw:mt-[26px] tw:flex tw:justify-end tw:items-center tw:gap-x-[26px]">
            <div>
                <x-button.blue class="tw:!h-[31px] tw:!rounded-lg tw:text-[1.2rem]">工事完工送信</x-button.blue>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('geProgressStep3', () => ({
                instanceMap: [
                    {
                        instanceId: @js('ge-progress-completion-photo-' . $geProgress->id),
                        uploadProperty: 'completionPhotoUploads',
                        saveMethod: 'saveCompletionPhotoUploads',
                        removeMethod: 'removeCompletionPhotoFile',
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
