<x-admin.auth-layout title="画像カテゴリ" :showPageTitle="true">
    <div class="tw:px-[26px] tw:pt-[42px]">
        <livewire:admin.master.image-category-master.item-list :category-kind="$categoryKind" />
    </div>
</x-admin.auth-layout>
