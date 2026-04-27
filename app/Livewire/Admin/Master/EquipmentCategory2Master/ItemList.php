<?php

namespace App\Livewire\Admin\Master\EquipmentCategory2Master;

use App\Models\EquipmentCategory1Master;
use App\Models\EquipmentCategory2Master;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ItemList extends Component
{
    public $equipmentCategory1MasterOptions = null;
    public $selectedEquipmentCategory1MasterId = '';
    public $equipmentCategory2Masters = null;
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
        $this->loadEquipmentCategory1MasterOptions();
        $this->loadEquipmentCategory2Masters();
    }

    public function updatedSelectedEquipmentCategory1MasterId()
    {
        $this->loadEquipmentCategory2Masters();
    }

    public function openEditDialog($id)
    {
        $equipmentCategory2Master = EquipmentCategory2Master::query()->find($id);
        if (!$equipmentCategory2Master) {
            return;
        }

        $this->resetValidation();
        $this->editingId = $equipmentCategory2Master->id;
        $this->editingItemName = (string) $equipmentCategory2Master->item_name;
        $this->isCreateMode = false;

        $this->dispatch('open-equipment-category2-edit-modal');
    }

    public function openCreateDialog()
    {
        if ($this->selectedEquipmentCategory1MasterId === '' || $this->selectedEquipmentCategory1MasterId === null) {
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
            $selectedCategory1MasterId = $this->selectedEquipmentCategory1MasterId;
            if ($selectedCategory1MasterId === '' || $selectedCategory1MasterId === null) {
                return;
            }

            DB::transaction(function () use ($validated, $selectedCategory1MasterId) {
                $maxDispRank = EquipmentCategory2Master::query()
                    ->where('equipment_category1_master_id', $selectedCategory1MasterId)
                    ->max('disp_rank');

                $nextDispRank = $maxDispRank === null ? 1 : ((int) $maxDispRank + 1);

                EquipmentCategory2Master::query()->create([
                    'equipment_category1_master_id' => (int) $selectedCategory1MasterId,
                    'item_name' => $validated['editingItemName'],
                    'disp_rank' => $nextDispRank,
                ]);
            });
        } else {
            if (!$this->editingId) {
                return;
            }

            DB::transaction(function () use ($validated) {
                $equipmentCategory2Master = EquipmentCategory2Master::query()->find($this->editingId);
                if (!$equipmentCategory2Master) {
                    return;
                }

                $equipmentCategory2Master->item_name = $validated['editingItemName'];
                $equipmentCategory2Master->save();
            });
        }

        $this->loadEquipmentCategory2Masters();
        $this->closeEditDialog();
    }

    public function moveUp($id)
    {
        $current = EquipmentCategory2Master::query()->find($id);
        if (!$current || $current->disp_rank === null) {
            return;
        }

        $target = EquipmentCategory2Master::query()
            ->where('id', '!=', $current->id)
            ->where('equipment_category1_master_id', $current->equipment_category1_master_id)
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

        $this->loadEquipmentCategory2Masters();
    }

    public function moveDown($id)
    {
        $current = EquipmentCategory2Master::query()->find($id);
        if (!$current || $current->disp_rank === null) {
            return;
        }

        $target = EquipmentCategory2Master::query()
            ->where('id', '!=', $current->id)
            ->where('equipment_category1_master_id', $current->equipment_category1_master_id)
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

        $this->loadEquipmentCategory2Masters();
    }

    public function deleteItem($id)
    {
        DB::transaction(function () use ($id) {
            $target = EquipmentCategory2Master::query()->find($id);
            if (!$target) {
                return;
            }

            $parentId = $target->equipment_category1_master_id;
            $target->delete();

            $siblingsQuery = EquipmentCategory2Master::query()
                ->orderBy('disp_rank', 'asc')
                ->orderBy('id', 'asc');

            if ($parentId === null) {
                $siblingsQuery->whereNull('equipment_category1_master_id');
            } else {
                $siblingsQuery->where('equipment_category1_master_id', $parentId);
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

        $this->loadEquipmentCategory2Masters();
    }

    private function loadEquipmentCategory2Masters()
    {
        if ($this->selectedEquipmentCategory1MasterId === '' || $this->selectedEquipmentCategory1MasterId === null) {
            $this->equipmentCategory2Masters = collect();
            return;
        }

        $this->equipmentCategory2Masters = EquipmentCategory2Master::query()
            ->where('equipment_category1_master_id', $this->selectedEquipmentCategory1MasterId)
            ->orderBy('disp_rank', 'asc')
            ->get();
    }

    private function loadEquipmentCategory1MasterOptions()
    {
        $options = EquipmentCategory1Master::query()
            ->orderBy('disp_rank', 'asc')
            ->pluck('item_name', 'id');

        $this->equipmentCategory1MasterOptions = $options->toArray();
        if (($this->selectedEquipmentCategory1MasterId === '' || $this->selectedEquipmentCategory1MasterId === null) && $options->isNotEmpty()) {
            $this->selectedEquipmentCategory1MasterId = (string) $options->keys()->first();
        }
    }

    public function render()
    {
        return view('livewire.admin.master.equipment-category2-master.item-list');
    }
}
