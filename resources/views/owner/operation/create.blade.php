<x-admin.auth-layout title="オペレーション作成" class="tw:overflow-auto">
    <form
        method="post"
        action="{{ route('admin.operation.store') }}"
        enctype="multipart/form-data"
        x-data="{ isDraft: 0 }"
        x-ref="form"
    >
        @csrf
        <livewire:admin.operation.create :operationId="$operationId" :teProgressId="$teProgressId" />
        <div class="tw:w-[1596px] tw:mx-[20px] tw:py-[21px] tw:flex tw:justify-between">
            <div>
                <x-button.gray class="tw:text-[1.4rem]">キャンセル</x-button.gray>
            </div>
            <div class="tw:flex tw:gap-x-[38px]">
                <x-button.gray
                    type="button"
                    class="tw:text-[1.4rem]"
                    @click="isDraft = 1; $nextTick(() => $refs.form.submit())"
                >
                    下書き保存
                </x-button.gray>
                <x-button.blue type="submit" class="tw:text-[1.4rem]">保存</x-button.blue>
            </div>
        </div>
        <input type="hidden" name="is_draft" :value="isDraft">
    </form>
</x-admin.auth-layout>
