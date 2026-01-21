<x-owner.auth-layout title="オペレーション一覧">
    <div class="tw:flex tw:flex-col tw:h-full">
        <div class="tw:pl-[26px] tw:py-[21px]">
            <div class="tw:text-[2.7rem] tw:font-bold">すべての物件</div>
            <div class="tw:flex tw:gap-x-[52px]">
                <div>
                    オペレーション種別
                    <x-form.select-search name="operation_kind_id" :options="$operationKindOptions" :empty="true" :value="$conditions['operation_kind_id'] ?? ''" class="tw:w-[208px]" />
                </div>
                <div>
                    ステータス
                    <x-form.select-search name="thread_status" :options="$threadStatusOptions" :empty="true" :value="$conditions['thread_status'] ?? ''" class="" />
                </div>
            </div>
        </div>
        <div class="tw:flex-1 tw:overflow-y-auto">
            <div class="tw:h-fit">
                <livewire:owner.operation.operation-list :threads="$threads" />
            </div>
        </div>
    </div>
</x-owner.auth-layout>
