<?php

namespace App\Livewire\Admin\RepairMap;

use App\Models\EquipmentCategory1Master;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MapList extends Component
{
    public $equipmentCategory1Masters = null;
    public $editingId = null;
    public $editingItemName = '';

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
        $this->loadEquipmentCategory1Masters();
    }

    public function openEditDialog($id)
    {
        $equipmentCategory1Master = EquipmentCategory1Master::query()->find($id);
        if (!$equipmentCategory1Master) {
            return;
        }

        $this->resetValidation();
        $this->editingId = $equipmentCategory1Master->id;
        $this->editingItemName = (string) $equipmentCategory1Master->item_name;

        $this->dispatch('open-equipment-category1-edit-modal');
    }

    public function closeEditDialog()
    {
        $this->editingId = null;
        $this->editingItemName = '';
        $this->resetValidation();

        $this->dispatch('close-equipment-category1-edit-modal');
    }

    public function saveEditItemName()
    {
        if (!$this->editingId) {
            return;
        }

        $validated = $this->validate();

        DB::transaction(function () use ($validated) {
            $equipmentCategory1Master = EquipmentCategory1Master::query()->find($this->editingId);
            if (!$equipmentCategory1Master) {
                return;
            }

            $equipmentCategory1Master->item_name = $validated['editingItemName'];
            $equipmentCategory1Master->save();
        });

        $this->loadEquipmentCategory1Masters();
        $this->closeEditDialog();
    }

    public function moveUp($id)
    {
        $current = EquipmentCategory1Master::query()->find($id);
        if (!$current || $current->disp_rank === null) {
            return;
        }

        $target = EquipmentCategory1Master::query()
            ->where('id', '!=', $current->id)
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

        $this->loadEquipmentCategory1Masters();
    }

    public function moveDown($id)
    {
        $current = EquipmentCategory1Master::query()->find($id);
        if (!$current || $current->disp_rank === null) {
            return;
        }

        $target = EquipmentCategory1Master::query()
            ->where('id', '!=', $current->id)
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

        $this->loadEquipmentCategory1Masters();
    }

    private function loadEquipmentCategory1Masters()
    {
        $this->equipmentCategory1Masters = EquipmentCategory1Master::query()
            ->orderBy('disp_rank', 'asc')
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.repair-map.map-list');
    }
}
