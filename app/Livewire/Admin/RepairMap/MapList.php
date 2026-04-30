<?php

namespace App\Livewire\Admin\RepairMap;

use App\Models\Category1Master;
use App\Models\Category2Master;
use App\Models\Category3Master;
use App\Models\EquipmentCategory1Master;
use App\Models\EquipmentCategory2Master;
use App\Models\TradingCompanyRank;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MapList extends Component
{
    public $equipmentCategory1Masters = null;
    public $pcCategory1MasterOptions = [];
    public $pcCategory2MasterOptions = [];
    public $pcCategory3MasterOptions = [];
    public $selectedPcCategory1MasterId = '';
    public $selectedPcCategory2MasterId = '';
    public $selectedPcCategory3MasterId = '';
    public $commonCategory1MasterOptions = [];
    public $commonCategory2MasterOptions = [];
    public $selectedCommonCategory1MasterId = '';
    public $selectedCommonCategory2MasterId = '';
    public $investmentSearchKeyword = '';
    public $tradingCompanyRanks = null;
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
        $this->loadPcCategory1MasterOptions();
        $this->loadPcCategory2MasterOptions();
        $this->loadPcCategory3MasterOptions();
        $this->loadCommonCategory1MasterOptions();
        $this->loadCommonCategory2MasterOptions();
        $this->loadTradingCompanyRanks();
    }

    public function updatedSelectedPcCategory1MasterId()
    {
        $this->loadPcCategory2MasterOptions();
        if (!$this->optionContainsValue($this->selectedPcCategory2MasterId, $this->pcCategory2MasterOptions)) {
            $this->selectedPcCategory2MasterId = '';
        }

        $this->loadPcCategory3MasterOptions();
        if (!$this->optionContainsValue($this->selectedPcCategory3MasterId, $this->pcCategory3MasterOptions)) {
            $this->selectedPcCategory3MasterId = '';
        }

        $this->dispatchPcCategory2MasterOptions();
        $this->dispatchPcCategory3MasterOptions();
        $this->loadTradingCompanyRanks();
    }

    public function updatedSelectedPcCategory2MasterId()
    {
        $this->loadPcCategory3MasterOptions();
        if (!$this->optionContainsValue($this->selectedPcCategory3MasterId, $this->pcCategory3MasterOptions)) {
            $this->selectedPcCategory3MasterId = '';
        }

        $this->dispatchPcCategory3MasterOptions();
        $this->loadTradingCompanyRanks();
    }

    public function updatedSelectedPcCategory3MasterId()
    {
        $this->loadTradingCompanyRanks();
    }

    public function updatedSelectedCommonCategory1MasterId()
    {
        $this->loadCommonCategory2MasterOptions();
        if (!$this->optionContainsValue($this->selectedCommonCategory2MasterId, $this->commonCategory2MasterOptions)) {
            $this->selectedCommonCategory2MasterId = '';
        }

        $this->dispatchCommonCategory2MasterOptions();
        $this->loadTradingCompanyRanks();
    }

    public function updatedSelectedCommonCategory2MasterId()
    {
        $this->loadTradingCompanyRanks();
    }

    public function updatedInvestmentSearchKeyword()
    {
        $this->loadTradingCompanyRanks();
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

    private function loadPcCategory1MasterOptions(): void
    {
        $this->pcCategory1MasterOptions = Category1Master::query()
            ->orderBy('id')
            ->pluck('item_name', 'id')
            ->toArray();
    }

    private function loadPcCategory2MasterOptions(): void
    {
        if ($this->selectedPcCategory1MasterId === '' || $this->selectedPcCategory1MasterId === null) {
            $this->pcCategory2MasterOptions = [];
            return;
        }

        $this->pcCategory2MasterOptions = Category2Master::query()
            ->where('category1_master_id', $this->selectedPcCategory1MasterId)
            ->orderBy('id')
            ->pluck('item_name', 'id')
            ->toArray();
    }

    private function loadPcCategory3MasterOptions(): void
    {
        if ($this->selectedPcCategory2MasterId === '' || $this->selectedPcCategory2MasterId === null) {
            $this->pcCategory3MasterOptions = [];
            return;
        }

        $this->pcCategory3MasterOptions = Category3Master::query()
            ->where('category2_master_id', $this->selectedPcCategory2MasterId)
            ->orderBy('id')
            ->pluck('item_name', 'id')
            ->toArray();
    }

    private function loadCommonCategory1MasterOptions(): void
    {
        $this->commonCategory1MasterOptions = EquipmentCategory1Master::query()
            ->orderBy('disp_rank', 'asc')
            ->orderBy('id', 'asc')
            ->pluck('item_name', 'id')
            ->toArray();
    }

    private function loadCommonCategory2MasterOptions(): void
    {
        if ($this->selectedCommonCategory1MasterId === '' || $this->selectedCommonCategory1MasterId === null) {
            $this->commonCategory2MasterOptions = [];
            return;
        }

        $this->commonCategory2MasterOptions = EquipmentCategory2Master::query()
            ->where('equipment_category1_master_id', $this->selectedCommonCategory1MasterId)
            ->orderBy('disp_rank', 'asc')
            ->orderBy('id', 'asc')
            ->pluck('item_name', 'id')
            ->toArray();
    }

    private function optionContainsValue($value, array $options): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        $target = (string) $value;
        foreach (array_keys($options) as $optionKey) {
            if ((string) $optionKey === $target) {
                return true;
            }
        }

        return false;
    }

    private function dispatchPcCategory2MasterOptions(): void
    {
        $this->dispatch(
            'select-search-options',
            name: 'pc_category2_master_id',
            options: $this->pcCategory2MasterOptions,
            value: $this->selectedPcCategory2MasterId === null ? '' : (string) $this->selectedPcCategory2MasterId,
        );
    }

    private function dispatchPcCategory3MasterOptions(): void
    {
        $this->dispatch(
            'select-search-options',
            name: 'pc_category3_master_id',
            options: $this->pcCategory3MasterOptions,
            value: $this->selectedPcCategory3MasterId === null ? '' : (string) $this->selectedPcCategory3MasterId,
        );
    }

    private function dispatchCommonCategory2MasterOptions(): void
    {
        $this->dispatch(
            'select-search-options',
            name: 'common_category2_master_id',
            options: $this->commonCategory2MasterOptions,
            value: $this->selectedCommonCategory2MasterId === null ? '' : (string) $this->selectedCommonCategory2MasterId,
        );
    }

    private function loadTradingCompanyRanks(): void
    {
        $searchKeyword = trim((string) $this->investmentSearchKeyword);
        $hasCategoryFilter =
            ($this->selectedPcCategory1MasterId !== '' && $this->selectedPcCategory1MasterId !== null) ||
            ($this->selectedPcCategory2MasterId !== '' && $this->selectedPcCategory2MasterId !== null) ||
            ($this->selectedPcCategory3MasterId !== '' && $this->selectedPcCategory3MasterId !== null) ||
            ($this->selectedCommonCategory1MasterId !== '' && $this->selectedCommonCategory1MasterId !== null) ||
            ($this->selectedCommonCategory2MasterId !== '' && $this->selectedCommonCategory2MasterId !== null);
        $hasSearchFilter = $searchKeyword !== '';

        if (!$hasCategoryFilter && !$hasSearchFilter) {
            $this->tradingCompanyRanks = collect();
            return;
        }

        $query = TradingCompanyRank::query()
            ->with([
                'tradingCompany',
                'category2Master',
                'category3Master',
                'equipmentCategory1Master',
                'equipmentCategory2Master',
            ])
            ->where('deleted', 0);

        if ($this->selectedPcCategory1MasterId !== '' && $this->selectedPcCategory1MasterId !== null) {
            $query->where('category1_master_id', $this->selectedPcCategory1MasterId);
        }
        if ($this->selectedPcCategory2MasterId !== '' && $this->selectedPcCategory2MasterId !== null) {
            $query->where('category2_master_id', $this->selectedPcCategory2MasterId);
        }
        if ($this->selectedPcCategory3MasterId !== '' && $this->selectedPcCategory3MasterId !== null) {
            $query->where('category3_master_id', $this->selectedPcCategory3MasterId);
        }
        if ($this->selectedCommonCategory1MasterId !== '' && $this->selectedCommonCategory1MasterId !== null) {
            $query->where('equipment_category1_master_id', $this->selectedCommonCategory1MasterId);
        }
        if ($this->selectedCommonCategory2MasterId !== '' && $this->selectedCommonCategory2MasterId !== null) {
            $query->where('equipment_category2_master_id', $this->selectedCommonCategory2MasterId);
        }

        if ($hasSearchFilter) {
            $escaped = addcslashes($searchKeyword, '\\%_');
            $searchLike = '%' . $escaped . '%';
            $isNumericKeyword = preg_match('/^\d+$/', $searchKeyword) === 1;
            $exactId = $isNumericKeyword ? (int) $searchKeyword : null;

            $query->whereHas('tradingCompany.investments', function ($investmentQuery) use ($searchLike, $isNumericKeyword, $exactId) {
                $investmentQuery->where(function ($q) use ($searchLike, $isNumericKeyword, $exactId) {
                    $q->where('investments.investment_name', 'like', $searchLike)
                        ->orWhereHas('landlord.owner', function ($ownerQuery) use ($searchLike, $isNumericKeyword, $exactId) {
                            $ownerQuery->where('name', 'like', $searchLike);

                            if ($isNumericKeyword) {
                                $ownerQuery->orWhere('id', $exactId);
                            }
                        });

                    if ($isNumericKeyword) {
                        $q->orWhere('investments.id', $exactId);
                    }
                });
            });
        }

        $this->tradingCompanyRanks = $query
            ->orderByRaw('rank IS NULL, rank ASC')
            ->orderBy('id', 'asc')
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.repair-map.map-list');
    }
}
