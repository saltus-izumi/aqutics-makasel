<div class="tw:w-[806px]">
    <div class="tw:w-full tw:pl-1 tw:bg-[#f3f3f3] tw:text-[1.1rem]">
        STEP７（原復発注）
    </div>
    <div class="tw:w-full tw:mt-[42px] tw:px-[26px]">
        <div class="tw:w-full">
            <x-form.multi_file_upload2
                name="other_completion_photo"
                title="発注書"
                instanceId="ge-progress-other-completion-photo-{{ $progress->id }}"
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
                :files="$otherCompletionPhotoFiles"
            />
        </div>
        <div class="tw:mt-[21px]">
            実行担当 ⇒ 原復会社<br>
            <x-form.textarea class="tw:!h-[105px]" placeholder="引継ぎコメント" wire:model.live="completionMessage"></x-form.textarea>
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
        const initGeProgressStep3Uploader = () => {
            const componentId = @js($componentId);
            const component = Livewire.find(componentId);
            const instanceMap = [
                {
                    instanceId: @js('ge-progress-other-completion-photo-' . $progress->id),
                    uploadProperty: 'otherCompletionPhotoUploads',
                    saveMethod: 'saveOtherCompletionPhotoUploads',
                    removeMethod: 'removeOtherCompletionPhotoFile',
                },
            ];

            if (!component) {
                return;
            }

            const handleSelect = (event) => {
                const target = instanceMap.find((item) => item.instanceId === event?.detail?.instanceId);
                if (!target) {
                    return;
                }
                const files = event.detail.files || [];
                if (files.length === 0) {
                    return;
                }
                component.uploadMultiple(target.uploadProperty, files, () => {
                    component.call(target.saveMethod);
                });
            };

            const handleRemove = (event) => {
                const target = instanceMap.find((item) => item.instanceId === event?.detail?.instanceId);
                if (!target) {
                    return;
                }
                const fileId = event?.detail?.file?.id || null;
                component.call(target.removeMethod, fileId);
            };

            window.addEventListener('multi-file-upload2:selected', handleSelect);
            window.addEventListener('multi-file-upload2:removed', handleRemove);
        };

        document.addEventListener('livewire:initialized', initGeProgressStep3Uploader);
    </script>
@endpush
