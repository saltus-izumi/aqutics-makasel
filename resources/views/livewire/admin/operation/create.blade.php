<div>
    <div class="tw:border-b tw:pb-[42px]">
        <x-form.select-search2 name="operation_kind_id" :options="$operationKindOptions" class="tw:text-[1.2rem] tw:w-[532px]" />
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
        </div>
    </div>
</div>
