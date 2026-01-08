<div>
    <div class="tw:border-b tw:pb-[42px]">
        <x-form.select-search2 name="operation_kind_id" wire:model.live="operation_kind_id" :options="$operationKindOptions" :value="$operation_kind_id" class="tw:text-[1.2rem] tw:w-[532px]" />
    </div>
    <div class="tw:pt-[21px] tw:flex tw:gap-x-[38px]">
        <div class="tw:w-[532px] tw:flex tw:flex-col tw:gap-y-[21px]">
            <div>
                <div class="tw:pb-1">
                    オーナー<x-badge.red class="tw:ml-1">必須</x-badge.red>
                </div>
                <x-form.select-search2 name="owner_id" wire:model.live="owner_id" :options="$ownerOptions" :value="$owner_id" class="tw:text-[1.2rem]" />
            </div>
            <div>
                <div class="tw:pb-1">
                    物件<x-badge.red class="tw:ml-1">必須</x-badge.red>
                </div>
                <x-form.select-search2 name="investment_id" wire:model.live="investment_id" :options="$investmentOptions" :value="$investment_id" class="tw:text-[1.2rem]" />
            </div>
            <div>
                <div class="tw:pb-1">
                    部屋<x-badge.red class="tw:ml-1">必須</x-badge.red>
                </div>
                <x-form.select-search2 name="investment_room_id" wire:model.live="investment_room_id" :options="$investmentRoomOptions" :value="$investment_room_id" class="tw:text-[1.2rem]" />
            </div>
            <div>
                <div class="tw:pb-1">
                    カテゴリ(通知種別) <x-badge.red class="tw:ml-1">必須</x-badge.red>
                </div>
                <x-form.select-search2 name="operation_template_id" wire:model.live="operation_template_id" :options="$operationTemplateOptions" :value="$operation_template_id" class="tw:text-[1.2rem]" />
            </div>
        </div>
    </div>
</div>
