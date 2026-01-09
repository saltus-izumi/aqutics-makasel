<div class="tw:px-[20px] tw:w-fit">
    <div class="tw:border-b tw:pb-[21px] tw:h-[72px] tw:flex tw:items-end">
        <x-form.select-search name="operation_kind_id" wire:model.live="operation_kind_id" :options="$operationKindOptions" :value="$operation_kind_id" class="tw:text-[1.2rem] tw:w-[532px]" />
    </div>
    <div class="tw:pt-[21px] tw:flex tw:gap-x-[38px]">
        <div class="tw:w-[532px] tw:flex tw:flex-col tw:gap-y-[21px]">
            <div>
                <div class="tw:pb-1">
                    オーナー<x-badge.red class="tw:ml-1">必須</x-badge.red>
                </div>
                <x-form.select-search name="owner_id" wire:model.live="owner_id" :options="$ownerOptions" :value="$owner_id" class="tw:text-[1.2rem]" />
                <x-form.error-message>{{ $errors->first('owner_id') }}</x-form.error-message>
            </div>
            <div>
                <div class="tw:pb-1">
                    物件<x-badge.red class="tw:ml-1">必須</x-badge.red>
                </div>
                <x-form.select-search name="investment_id" wire:model.live="investment_id" :options="$investmentOptions" :value="$investment_id" class="tw:text-[1.2rem]" />
                <x-form.error-message>{{ $errors->first('investment_id') }}</x-form.error-message>
            </div>
            <div>
                <div class="tw:pb-1">
                    部屋<x-badge.red class="tw:ml-1">必須</x-badge.red>
                </div>
                <x-form.select-search name="investment_room_id" wire:model.live="investment_room_id" :options="$investmentRoomOptions" :value="$investment_room_id" class="tw:text-[1.2rem]" />
                <x-form.error-message>{{ $errors->first('investment_room_id') }}</x-form.error-message>
            </div>
            <div>
                <div class="tw:pb-1">
                    カテゴリ(通知種別) <x-badge.red class="tw:ml-1">必須</x-badge.red>
                </div>
                <x-form.select-search name="operation_template_id" wire:model.live="operation_template_id" :options="$operationTemplateOptions" :value="$operation_template_id" class="tw:text-[1.2rem]" />
                <x-form.error-message>{{ $errors->first('operation_template_id') }}</x-form.error-message>
            </div>
            <div>
                <div class="tw:pb-1">
                    テンプレート
                </div>
                <x-form.textarea name="template" class="tw:text-[1.2rem]" rows="10" wire:model="template"></x-form.textarea>
            </div>
        </div>
        <div class="tw:w-[1026px] tw:flex tw:flex-col tw:gap-y-[21px]">
            <div>
                <div class="tw:pb-1">
                    カスタマイズタイトル<x-badge.red class="tw:ml-1">必須</x-badge.red>
                </div>
                <x-form.input name="title" :value="$title" wire:model="title" class="tw:text-[1.2rem]" />
                <x-form.error-message>{{ $errors->first('title') }}</x-form.error-message>
            </div>
            <div>
                <div class="tw:pb-1">
                    カスタマイズ項目
                </div>
                <x-form.textarea name="message" class="tw:text-[1.2rem] tw:h-[502px]"></x-form.textarea>
            </div>
        </div>
    </div>
    <div class="tw:mt-[21px]">
        添付ファイル・画像、ＰＤＦ、Excel、Wordファイルが送信可能です。（可能ファイル数：20個／1ファイルの最大サイズ：25MB）
        <x-form.multi_file_upload class="tw:h-[84px]" maxFileCount="20" maxFileSize="25MB" />
    </div>
</div>
