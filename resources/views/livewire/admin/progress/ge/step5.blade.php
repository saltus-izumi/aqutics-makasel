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
