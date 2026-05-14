<div class="tw:h-full tw:w-full tw:overflow-auto">
    <div class="tw:h-[45px] tw:w-[calc(100%-40px)] tw:ml-[26px] tw:flex tw:items-end tw:border-b tw:mb-[21px]">
        @foreach (\App\Models\ImageCategoryMaster::SHORT_NAME as $tabCategoryKind => $tabName)
            @if ((string) $categoryKind === (string) $tabCategoryKind)
                <div class="tw:w-[130px] tw:h-[42px] tw:leading-[42px] tw:text-[1.4rem] tw:font-bold tw:text-center tw:bg-[#d9d9d9] tw:border-b tw:border-b-3 tw:border-b-pm_blue_001">{{ $tabName }}</div>
            @else
                <a href="{{ route('admin.master.image-category-master.index', ['categoryKind' => $tabCategoryKind]) }}"><div class="tw:w-[130px] tw:h-[42px] tw:leading-[42px] tw:text-[1.4rem] tw:font-bold tw:text-center tw:bg-[#efefef]">{{ $tabName }}</div></a>
            @endif
        @endforeach
    </div>
    <div class="tw:h-[calc(100%-165px)]">
        <div class="tw:px-[26px]">
            <div>
                <table class="tw:table-fixed tw:w-[728px] tw:min-w-[728px]">
                    <colgroup>
                        <col class="tw:w-[78px]">
                        <col class="tw:w-[390px]">
                        <col class="tw:w-[78px]">
                        <col class="tw:w-[182px]">
                    </colgroup>
                    <thead class="tw:sticky tw:top-0 tw:z-10">
                        <tr class="tw:h-[42px]">
                            <td class="tw:text-center tw:bg-[#efefef]">ID</td>
                            <td class="tw:text-center tw:bg-[#efefef]">カテゴリ名</td>
                            <td class="tw:text-center tw:bg-[#efefef]">表示順序</td>
                            <td class="tw:text-center tw:bg-[#efefef]"></td>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($imageCategoryMasters as $imageCategoryMaster)
                            <tr class="tw:h-[42px] tw:border-b tw:border-b-[#cccccc]">
                                <td class="tw:text-center">{{ $imageCategoryMaster->id }}</td>
                                <td>{{ $imageCategoryMaster->item_name }}</td>
                                <td class="tw:text-center">{{ $imageCategoryMaster->disp_rank }}</td>
                                <td class="tw:h-[41px] tw:flex tw:gap-x-[26px] tw:items-center">
                                    <x-button.blue
                                        class="tw:!h-[21px] tw:!min-w-[72px]"
                                        wire:click="openEditDialog({{ $imageCategoryMaster->id }})"
                                    >
                                        編集
                                    </x-button.blue>
                                    <div class="tw:flex tw:gap-x-[5px]">
                                        <x-button.gray
                                            class="tw:!h-[21px] tw:!min-w-[26px] tw:!px-[0]"
                                            wire:click="moveUp({{ $imageCategoryMaster->id }})"
                                        >
                                            <i class="fas fa-angle-double-up"></i>
                                        </x-button.gray>
                                        <x-button.gray
                                            class="tw:!h-[21px] tw:!min-w-[26px] tw:!px-[0]"
                                            wire:click="moveDown({{ $imageCategoryMaster->id }})"
                                        >
                                            <i class="fas fa-angle-double-down"></i>
                                        </x-button.gray>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="tw:h-[42px] tw:border-b tw:border-b-[#cccccc]">
                                <td colspan="4" class="tw:text-center tw:text-gray-500">該当データがありません</td>
                            </tr>
                        @endforelse
                        <tr>
                            <td class="tw:text-right" colspan="4">
                                <button
                                    type="button"
                                    class="tw:cursor-pointer"
                                    wire:click="openCreateDialog"
                                    aria-label="画像カテゴリ新規作成"
                                >
                                    <i class="fas fa-plus-square tw:text-[1.2rem] tw:text-[#4a86e8]"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-modal :title="$isCreateMode ? '画像カテゴリ新規作成' : '画像カテゴリ編集'" event="image-category-edit-modal">
        <form wire:submit.prevent="saveItem" class="tw:flex tw:flex-col tw:gap-y-[12px]">
            <div>
                <div class="tw:pb-1">
                    カテゴリ名<x-badge.red class="tw:ml-1">必須</x-badge.red>
                </div>
                <x-form.input
                    name="editing_item_name"
                    wire:model="editingItemName"
                    class="tw:text-[1.2rem] tw:!h-[42px]"
                    :is_error="$errors->has('editingItemName')"
                />
                <x-form.error-message>{{ $errors->first('editingItemName') }}</x-form.error-message>
            </div>

            <div class="tw:flex tw:justify-end tw:gap-x-[8px]">
                <x-button.gray type="button" wire:click="closeEditDialog">キャンセル</x-button.gray>
                <x-button.blue type="submit">保存</x-button.blue>
            </div>
        </form>
    </x-modal>
</div>
