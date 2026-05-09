<?php

namespace App\Livewire\Admin\Master\Category2Master;

use App\Models\Category1Master;
use App\Models\Category2Master;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ItemList extends Component
{
    public $category1MasterOptions = null;
    public $selectedCategory1MasterId = '';
    public $category2Masters = null;
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

    public function mount()
    {
        $this->loadCategory1MasterOptions();
        $this->loadCategory2Masters();
    }

    public function updatedSelectedCategory1MasterId()
    {
        $this->loadCategory2Masters();
    }

    public function openEditDialog($id)
    {
        $category2Master = Category2Master::query()
            ->where('category1_master_id', $this->selectedCategory1MasterId)
            ->find($id);
        if (!$category2Master) {
            return;
        }

        $this->resetValidation();
        $this->editingId = $category2Master->id;
        $this->editingItemName = (string) $category2Master->item_name;
        $this->isCreateMode = false;

        $this->dispatch('open-equipment-category2-edit-modal');
    }

    public function openCreateDialog()
    {
        if (!$this->hasSelectedCategory1Master()) {
            return;
        }

        $this->resetValidation();
        $this->editingId = null;
        $this->editingItemName = '';
        $this->isCreateMode = true;

        $this->dispatch('open-equipment-category2-edit-modal');
    }

    public function closeEditDialog()
    {
        $this->editingId = null;
        $this->editingItemName = '';
        $this->isCreateMode = false;
        $this->resetValidation();

        $this->dispatch('close-equipment-category2-edit-modal');
    }

    public function saveItem()
    {
        $validated = $this->validate();

        if ($this->isCreateMode) {
            $selectedCategory1MasterId = $this->selectedCategory1MasterId;
            if (!$this->hasSelectedCategory1Master()) {
                return;
            }

            DB::transaction(function () use ($validated, $selectedCategory1MasterId) {
                $maxDispRank = Category2Master::query()
                    ->where('category1_master_id', $selectedCategory1MasterId)
                    ->max('disp_rank');

                $nextDispRank = $maxDispRank === null ? 1 : ((int) $maxDispRank + 1);

                DB::table('category2_masters')->insert([
                    'category1_master_id' => (int) $selectedCategory1MasterId,
                    'item_name' => $validated['editingItemName'],
                    'disp_rank' => $nextDispRank,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
        } else {
            if (!$this->editingId) {
                return;
            }

            DB::transaction(function () use ($validated) {
                $category2Master = Category2Master::query()
                    ->where('category1_master_id', $this->selectedCategory1MasterId)
                    ->find($this->editingId);
                if (!$category2Master) {
                    return;
                }

                $category2Master->item_name = $validated['editingItemName'];
                $category2Master->save();
            });
        }

        $this->loadCategory2Masters();
        $this->closeEditDialog();
    }

    public function moveUp($id)
    {
        $current = Category2Master::query()
            ->where('category1_master_id', $this->selectedCategory1MasterId)
            ->find($id);
        if (!$current || $current->disp_rank === null) {
            return;
        }

        $target = Category2Master::query()
            ->where('id', '!=', $current->id)
            ->where('category1_master_id', $current->category1_master_id)
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
            $targetDispRank = $target->disp_rank;

            $current->disp_rank = $targetDispRank;
            $current->save();

            $target->disp_rank = $currentDispRank;
            $target->save();
        });

        $this->loadCategory2Masters();
    }

    public function moveDown($id)
    {
        $current = Category2Master::query()
            ->where('category1_master_id', $this->selectedCategory1MasterId)
            ->find($id);
        if (!$current || $current->disp_rank === null) {
            return;
        }

        $target = Category2Master::query()
            ->where('id', '!=', $current->id)
            ->where('category1_master_id', $current->category1_master_id)
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
            $targetDispRank = $target->disp_rank;

            $current->disp_rank = $targetDispRank;
            $current->save();

            $target->disp_rank = $currentDispRank;
            $target->save();
        });

        $this->loadCategory2Masters();
    }

    public function deleteItem($id)
    {
        DB::transaction(function () use ($id) {
            $target = Category2Master::query()
                ->where('category1_master_id', $this->selectedCategory1MasterId)
                ->find($id);
            if (!$target) {
                return;
            }

            $parentId = $target->category1_master_id;
            $target->delete();

            $siblingsQuery = Category2Master::query()
                ->orderBy('disp_rank', 'asc')
                ->orderBy('id', 'asc');

            if ($parentId === null) {
                $siblingsQuery->whereNull('category1_master_id');
            } else {
                $siblingsQuery->where('category1_master_id', $parentId);
            }

            $siblings = $siblingsQuery->get();
            foreach ($siblings as $index => $sibling) {
                $newDispRank = $index + 1;
                if ((int) $sibling->disp_rank !== $newDispRank) {
                    $sibling->disp_rank = $newDispRank;
                    $sibling->save();
                }
            }
        });

        $this->loadCategory2Masters();
    }

    private function loadCategory2Masters()
    {
        if (!$this->hasSelectedCategory1Master()) {
            $this->category2Masters = collect();
            return;
        }

        $this->category2Masters = Category2Master::query()
            ->where('category1_master_id', $this->selectedCategory1MasterId)
            ->orderBy('disp_rank', 'asc')
            ->get();
    }

    private function loadCategory1MasterOptions()
    {
        $options = Category1Master::query()
            ->orderBy('disp_rank', 'asc')
            ->pluck('item_name', 'id');

        $this->category1MasterOptions = $options->toArray();
        if (($this->selectedCategory1MasterId === '' || $this->selectedCategory1MasterId === null) && $options->isNotEmpty()) {
            $this->selectedCategory1MasterId = (string) $options->keys()->first();
        }
    }

    private function hasSelectedCategory1Master(): bool
    {
        return $this->selectedCategory1MasterId !== '' && $this->selectedCategory1MasterId !== null;
    }

    public function render()
    {
        return view('livewire.admin.master.category2-master.item-list');
    }
}
