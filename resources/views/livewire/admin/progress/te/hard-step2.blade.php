<div class="tw:w-[832px]">
    <div class="tw:w-full tw:pl-1 tw:bg-[#f3f3f3] tw:text-[1.1rem]">
        ハード｜STEP２（責任者＿適性判断）
    </div>
    <div class="tw:h-[42px] tw:flex-1 tw:flex tw:items-center tw:justify-end tw:gap-x-[26px]">
        <x-button.black
            class="tw:!h-[28px] tw:!px-[15px] tw:!rounded-lg tw:!w-[120px]"
            x-on:click="window.dispatchEvent(new CustomEvent('open-te-pc-status-remarks-modal'))"
        >
            PC対応履歴
        </x-button.black>
        <x-button.black
            class="tw:!h-[28px] tw:!px-[15px] tw:!rounded-lg tw:!w-[120px]"
            x-on:click="window.dispatchEvent(new CustomEvent('open-te-status-remarks-modal'))"
        >
            社内メモ
        </x-button.black>
    </div>
    <div class="tw:w-full tw:px-[26px]">
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
    </div>

    <x-modal title="PC対応履歴" event="te-pc-status-remarks-modal">
        <x-form.textarea
            name="pcStatusRemarks"
            class="tw:!h-[240px]"
            wire:model.live="pcStatusRemarks"
        ></x-form.textarea>
    </x-modal>

    <x-modal title="社内メモ" event="te-status-remarks-modal">
        <x-form.textarea
            name="statusRemarks"
            class="tw:!h-[240px]"
            wire:model.live="statusRemarks"
        ></x-form.textarea>
    </x-modal>
</div>
