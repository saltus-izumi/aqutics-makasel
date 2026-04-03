<div class="tw:w-[806px]">
    <div class="tw:w-full tw:pl-1 tw:bg-[#f3f3f3] tw:text-[1.1rem]">
        ハード｜STEP３（実行担当＿貸主提案）
    </div>
    <div class="tw:w-full tw:mt-[21px] tw:px-[26px]">
        <div class="tw:h-[42px] tw:flex tw:justify-center tw:items-start">
            @if ($teProgress?->owner_proposal_operation_id)
                <a href="{{ route('admin.operation.edit', [
                    'operationId' => $teProgress?->owner_proposal_operation_id,
                ]) }}">
                    <x-button.blue class="tw:!h-[31px] tw:!rounded-lg tw:text-[1.2rem]">オペレーション編集</x-button.blue>
                </a>
            @else
                <a href="{{ route('admin.operation.create.te', [
                    'teProgressId' => $teProgress?->id,
                    'progressStep' => 'owner_proposal',
                ]) }}">
                    <x-button.blue class="tw:!h-[31px] tw:!rounded-lg tw:text-[1.2rem]">オペレーション作成</x-button.blue>
                </a>
            @endif
        </div>
    </div>
</div>
