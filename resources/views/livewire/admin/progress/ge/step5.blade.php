<div class="tw:w-[832px]">
    <div class="tw:w-full tw:pl-1 tw:bg-[#f3f3f3] tw:text-[1.1rem]">
        STEP５（責任者＿引き算確認）
    </div>
    <div class="tw:w-full tw:mt-[21px] tw:px-[26px]">
        <div class="tw:w-full tw:flex tw:gap-x-[52px]">
            <div class="tw:flex-1 tw:flex">
                <div class="tw:w-[130px] tw:h-[42px] tw:text-[1.2rem] tw:text-center tw:leading-[42px] tw:bg-[#efefef] tw:border tw:border-[#cccccc]">
                    適正工事(負担)
                </div>
                <div class="tw:w-[234px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-l-0 tw:p-[1px]">
                    <x-form.select name="is_proper_work_burden" :options="App\Models\GeProgress::IS_PROPER_WORK_BURDEN" empty=" " class="tw:!w-full tw:h-full tw:text-[1.2rem]" wire:model.live="isProperWorkBurden" />
                </div>
            </div>
            <div class="tw:flex-1 tw:flex">
                <div class="tw:w-[130px] tw:h-[42px] tw:text-[1.2rem] tw:text-center tw:leading-[42px] tw:bg-[#efefef] tw:border tw:border-[#cccccc]">
                    適正価格
                </div>
                <div class="tw:w-[234px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-l-0 tw:p-[1px]">
                    <x-form.select name="is_proper_price" :options="App\Models\GeProgress::IS_PROPER_PRICE" empty=" " class="tw:!w-full tw:h-full tw:text-[1.2rem]" wire:model.live="isProperPrice" />
                </div>
            </div>
        </div>
        <div class="tw:mt-[21px]">
            実行担当へ修正指示<br>
            <x-form.textarea class="tw:!h-[105px]" placeholder="引継ぎコメント" wire:model.live="correctionInstructionMessage"></x-form.textarea>
        </div>
        <div class="tw:mt-[21px]">
            見積書備考入力内容<br>
            <x-form.textarea class="tw:!h-[105px]" placeholder="引継ぎコメント" wire:model.live="estimateNoteMessage"></x-form.textarea>
        </div>
        <div class="tw:h-[42px] tw:mt-[26px] tw:flex tw:justify-end tw:items-center tw:gap-x-[26px]">
            <div>
                <x-button.blue class="tw:!h-[31px] tw:!rounded-lg tw:text-[1.2rem]">所有者適正判断完了</x-button.blue>
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
