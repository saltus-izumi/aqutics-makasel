<x-owner.auth-layout title="オペレーション一覧" :investmentId="$investment?->id">
    <div class="tw:flex tw:flex-col tw:h-full">
        <form method="get" action="{{ route('owner.operation.index') }}">
            <div class="tw:pl-[26px] tw:py-[21px]">
                <div class="tw:text-[2.7rem] tw:font-bold">
                    {{ $investment ? $investment->investment_name : 'すべての物件' }}
                </div>
                <div class="tw:flex tw:gap-x-[52px]">
                    <div>
                        オペレーション種別
                        <x-form.select-search
                            name="operation_kind_id"
                            :options="$operationKindOptions"
                            :empty="true"
                            :value="$conditions['operation_kind_id'] ?? ''"
                            class="tw:w-[208px]"
                            x-on:input.debounce.200ms="$el.closest('form')?.requestSubmit()"
                        />
                    </div>
                    <div>
                        ステータス
                        <x-form.select-search
                            name="thread_status"
                            :options="$threadStatusOptions"
                            :empty="true"
                            :value="$conditions['thread_status'] ?? ''"
                            class="tw:w-[156px]"
                            x-on:input.debounce.200ms="$el.closest('form')?.requestSubmit()"
                        />
                    </div>
                </div>
            </div>
            <div class="tw:flex-1 tw:overflow-y-auto">
                <div class="tw:h-fit">
                    <livewire:owner.operation.operation-list :ownerId="auth('owner')->id()" :conditions="$conditions" />
                </div>
            </div>
            <input type="hidden" name="investment_id" value="{{ $conditions['investment_id'] ?? '' }}" />
        </form>
    </div>
</x-owner.auth-layout>
