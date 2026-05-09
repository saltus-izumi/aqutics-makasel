<?php

namespace App\Livewire\Admin\Master\Category3Master;

use App\Models\Category1Master;
use App\Models\Category2Master;
use App\Models\Category3Master;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ItemList extends Component
{
    public $category1MasterOptions = null;
    public $selectedCategory1MasterId = '';
    public $category2MasterOptions = null;
    public $selectedCategory2MasterId = '';
    public $category3Masters = null;
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
        $this->loadCategory2MasterOptions();
        $this->loadCategory3Masters();
    }

    public function updatedSelectedCategory1MasterId()
    {
        $this->selectedCategory2MasterId = '';
        $this->loadCategory2MasterOptions();
        $this->dispatchCategory2MasterOptions();
        $this->loadCategory3Masters();
    }

    public function updatedSelectedCategory2MasterId()
    {
        $this->loadCategory3Masters();
    }

    public function openEditDialog($id)
    {
        $category3Master = Category3Master::query()
            ->where('category2_master_id', $this->selectedCategory2MasterId)
            ->find($id);
        if (!$category3Master) {
            return;
        }

        $this->resetValidation();
        $this->editingId = $category3Master->id;
        $this->editingItemName = (string) $category3Master->item_name;
        $this->isCreateMode = false;

        $this->dispatch('open-category3-edit-modal');
    }

    public function openCreateDialog()
    {
        if (!$this->hasSelectedCategory2Master()) {
            return;
        }

        $this->resetValidation();
        $this->editingId = null;
        $this->editingItemName = '';
        $this->isCreateMode = true;

        $this->dispatch('open-category3-edit-modal');
    }

    public function closeEditDialog()
    {
        $this->editingId = null;
        $this->editingItemName = '';
        $this->isCreateMode = false;
        $this->resetValidation();

        $this->dispatch('close-category3-edit-modal');
    }

    public function saveItem()
    {
        $validated = $this->validate();

        if ($this->isCreateMode) {
            $selectedCategory2MasterId = $this->selectedCategory2MasterId;
            if (!$this->hasSelectedCategory2Master()) {
                return;
            }

            DB::transaction(function () use ($validated, $selectedCategory2MasterId) {
                $maxDispRank = Category3Master::query()
                    ->where('category2_master_id', $selectedCategory2MasterId)
                    ->max('disp_rank');

                DB::table('category3_masters')->insert([
                    'category2_master_id' => (int) $selectedCategory2MasterId,
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
                $category3Master = Category3Master::query()
                    ->where('category2_master_id', $this->selectedCategory2MasterId)
                    ->find($this->editingId);
                if (!$category3Master) {
                    return;
                }

                $category3Master->item_name = $validated['editingItemName'];
                $category3Master->save();
            });
        }

        $this->loadCategory3Masters();
        $this->closeEditDialog();
    }

    public function moveUp($id)
    {
        $current = Category3Master::query()
            ->where('category2_master_id', $this->selectedCategory2MasterId)
            ->find($id);
        if (!$current || $current->disp_rank === null) {
            return;
        }

        $target = Category3Master::query()
            ->where('id', '!=', $current->id)
            ->where('category2_master_id', $current->category2_master_id)
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

        $this->loadCategory3Masters();
    }

    public function moveDown($id)
    {
        $current = Category3Master::query()
            ->where('category2_master_id', $this->selectedCategory2MasterId)
            ->find($id);
        if (!$current || $current->disp_rank === null) {
            return;
        }

        $target = Category3Master::query()
            ->where('id', '!=', $current->id)
            ->where('category2_master_id', $current->category2_master_id)
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

        $this->loadCategory3Masters();
    }

    public function deleteItem($id)
    {
        DB::transaction(function () use ($id) {
            $target = Category3Master::query()
                ->where('category2_master_id', $this->selectedCategory2MasterId)
                ->find($id);
            if (!$target) {
                return;
            }

            $parentId = $target->category2_master_id;
            $target->delete();

            $siblings = Category3Master::query()
                ->where('category2_master_id', $parentId)
                ->orderBy('disp_rank', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($siblings as $index => $sibling) {
                $newDispRank = $index + 1;
                if ((int) $sibling->disp_rank !== $newDispRank) {
                    $sibling->disp_rank = $newDispRank;
                    $sibling->save();
                }
            }
        });

        $this->loadCategory3Masters();
    }

    private function loadCategory3Masters()
    {
        if (!$this->hasSelectedCategory2Master()) {
            $this->category3Masters = collect();
            return;
        }

        $this->category3Masters = Category3Master::query()
            ->where('category2_master_id', $this->selectedCategory2MasterId)
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

    private function loadCategory2MasterOptions()
    {
        if ($this->selectedCategory1MasterId === '' || $this->selectedCategory1MasterId === null) {
            $this->category2MasterOptions = [];
            return;
        }

        $options = Category2Master::query()
            ->where('category1_master_id', $this->selectedCategory1MasterId)
            ->orderBy('disp_rank', 'asc')
            ->pluck('item_name', 'id');

        $this->category2MasterOptions = $options->toArray();
        if (($this->selectedCategory2MasterId === '' || $this->selectedCategory2MasterId === null) && $options->isNotEmpty()) {
            $this->selectedCategory2MasterId = (string) $options->keys()->first();
        }
    }

    private function dispatchCategory2MasterOptions(): void
    {
        $this->dispatch(
            'select-search-options',
            name: 'category2_master_id',
            options: $this->category2MasterOptions,
            value: $this->selectedCategory2MasterId === null ? '' : (string) $this->selectedCategory2MasterId,
        );
    }

    private function hasSelectedCategory2Master(): bool
    {
        return $this->selectedCategory2MasterId !== '' && $this->selectedCategory2MasterId !== null;
    }

    public function render()
    {
        return view('livewire.admin.master.category3-master.item-list');
    }
}
