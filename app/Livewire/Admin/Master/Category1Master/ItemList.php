<?php

namespace App\Livewire\Admin\Master\Category1Master;

use App\Models\Category1Master;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ItemList extends Component
{
    public $category1Masters = null;
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
        $this->loadCategory1Masters();
    }

    public function openEditDialog($id)
    {
        $category1Master = Category1Master::query()
            ->where('id', '!=', Category1Master::EQUIPTMENT)
            ->find($id);
        if (!$category1Master) {
            return;
        }

        $this->resetValidation();
        $this->editingId = $category1Master->id;
        $this->editingItemName = (string) $category1Master->item_name;

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
            $category1Master = Category1Master::query()
                ->where('id', '!=', Category1Master::EQUIPTMENT)
                ->find($this->editingId);
            if (!$category1Master) {
                return;
            }

            $category1Master->item_name = $validated['editingItemName'];
            $category1Master->save();
        });

        $this->loadCategory1Masters();
        $this->closeEditDialog();
    }

    public function moveUp($id)
    {
        $current = Category1Master::query()
            ->where('id', '!=', Category1Master::EQUIPTMENT)
            ->find($id);
        if (!$current || $current->disp_rank === null) {
            return;
        }

        $target = Category1Master::query()
            ->where('id', '!=', $current->id)
            ->where('id', '!=', Category1Master::EQUIPTMENT)
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

        $this->loadCategory1Masters();
    }

    public function moveDown($id)
    {
        $current = Category1Master::query()
            ->where('id', '!=', Category1Master::EQUIPTMENT)
            ->find($id);
        if (!$current || $current->disp_rank === null) {
            return;
        }

        $target = Category1Master::query()
            ->where('id', '!=', $current->id)
            ->where('id', '!=', Category1Master::EQUIPTMENT)
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

        $this->loadCategory1Masters();
    }

    private function loadCategory1Masters()
    {
        $this->category1Masters = Category1Master::query()
            ->where('id', '!=', Category1Master::EQUIPTMENT)
            ->orderBy('disp_rank', 'asc')
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.master.category1-master.item-list');
    }
}
