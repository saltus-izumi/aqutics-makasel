<?php

namespace App\Livewire\Admin\Master\ImageCategoryMaster;

use App\Models\ImageCategoryMaster;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ItemList extends Component
{
    public $categoryKind = ImageCategoryMaster::CATEGORY_KIND_EXTERIOR;
    public $imageCategoryMasters = null;
    public $editingId = null;
    public $editingItemName = '';
    public $isCreateMode = false;

    protected function rules(): array
    {
        return [
            'editingItemName' => ['required', 'string', 'max:255'],
        ];
    }

    protected function messages(): array
    {
        return [
            'editingItemName.required' => 'カテゴリ名を入力してください。',
            'editingItemName.max' => 'カテゴリ名は255文字以内で入力してください。',
        ];
    }

    public function mount($categoryKind = ImageCategoryMaster::CATEGORY_KIND_EXTERIOR)
    {
        $this->categoryKind = $this->normalizeCategoryKind($categoryKind);
        $this->loadImageCategoryMasters();
    }

    public function openEditDialog($id)
    {
        $imageCategoryMaster = ImageCategoryMaster::query()
            ->where('category_kind', $this->categoryKind)
            ->find($id);
        if (!$imageCategoryMaster) {
            return;
        }

        $this->resetValidation();
        $this->editingId = $imageCategoryMaster->id;
        $this->editingItemName = (string) $imageCategoryMaster->item_name;
        $this->isCreateMode = false;

        $this->dispatch('open-image-category-edit-modal');
    }

    public function openCreateDialog()
    {
        $this->resetValidation();
        $this->editingId = null;
        $this->editingItemName = '';
        $this->isCreateMode = true;

        $this->dispatch('open-image-category-edit-modal');
    }

    public function closeEditDialog()
    {
        $this->editingId = null;
        $this->editingItemName = '';
        $this->isCreateMode = false;
        $this->resetValidation();

        $this->dispatch('close-image-category-edit-modal');
    }

    public function saveItem()
    {
        $validated = $this->validate();

        if ($this->isCreateMode) {
            $categoryKind = $this->categoryKind;

            DB::transaction(function () use ($validated, $categoryKind) {
                $maxDispRank = ImageCategoryMaster::query()
                    ->where('category_kind', $categoryKind)
                    ->max('disp_rank');

                DB::table('image_category_masters')->insert([
                    'category_kind' => (int) $categoryKind,
                    'item_name' => $validated['editingItemName'],
                    'disp_rank' => $maxDispRank === null ? 1 : ((int) $maxDispRank + 1),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
        } else {
            if (!$this->editingId) {
                return;
            }

            DB::transaction(function () use ($validated) {
                $imageCategoryMaster = ImageCategoryMaster::query()
                    ->where('category_kind', $this->categoryKind)
                    ->find($this->editingId);
                if (!$imageCategoryMaster) {
                    return;
                }

                $imageCategoryMaster->item_name = $validated['editingItemName'];
                $imageCategoryMaster->save();
            });
        }

        $this->loadImageCategoryMasters();
        $this->closeEditDialog();
    }

    public function moveUp($id)
    {
        $current = ImageCategoryMaster::query()
            ->where('category_kind', $this->categoryKind)
            ->find($id);
        if (!$current || $current->disp_rank === null) {
            return;
        }

        $target = ImageCategoryMaster::query()
            ->where('id', '!=', $current->id)
            ->where('category_kind', $current->category_kind)
            ->whereNotNull('disp_rank')
            ->where('disp_rank', '<', $current->disp_rank)
            ->orderBy('disp_rank', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        if (!$target) {
            return;
        }

        DB::transaction(function () use ($current, $target) {
            $currentDispRank = $current->disp_rank;
            $current->disp_rank = $target->disp_rank;
            $current->save();

            $target->disp_rank = $currentDispRank;
            $target->save();
        });

        $this->loadImageCategoryMasters();
    }

    public function moveDown($id)
    {
        $current = ImageCategoryMaster::query()
            ->where('category_kind', $this->categoryKind)
            ->find($id);
        if (!$current || $current->disp_rank === null) {
            return;
        }

        $target = ImageCategoryMaster::query()
            ->where('id', '!=', $current->id)
            ->where('category_kind', $current->category_kind)
            ->whereNotNull('disp_rank')
            ->where('disp_rank', '>', $current->disp_rank)
            ->orderBy('disp_rank', 'asc')
            ->orderBy('id', 'asc')
            ->first();

        if (!$target) {
            return;
        }

        DB::transaction(function () use ($current, $target) {
            $currentDispRank = $current->disp_rank;
            $current->disp_rank = $target->disp_rank;
            $current->save();

            $target->disp_rank = $currentDispRank;
            $target->save();
        });

        $this->loadImageCategoryMasters();
    }

    private function loadImageCategoryMasters()
    {
        $this->imageCategoryMasters = ImageCategoryMaster::query()
            ->where('category_kind', $this->categoryKind)
            ->orderBy('disp_rank', 'asc')
            ->get();
    }

    private function normalizeCategoryKind($categoryKind): string
    {
        $categoryKind = (string) $categoryKind;

        if (!array_key_exists($categoryKind, ImageCategoryMaster::SHORT_NAME)) {
            return ImageCategoryMaster::CATEGORY_KIND_EXTERIOR;
        }

        return $categoryKind;
    }

    public function render()
    {
        return view('livewire.admin.master.image-category-master.item-list');
    }
}
