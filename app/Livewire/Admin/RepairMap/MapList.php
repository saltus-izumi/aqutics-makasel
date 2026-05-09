<?php

namespace App\Livewire\Admin\RepairMap;

use App\Models\Category1Master;
use App\Models\Category2Master;
use App\Models\Category3Master;
use App\Models\TradingCompanyRank;
use Livewire\Component;

class MapList extends Component
{
    public $pcCategory1MasterOptions = [];
    public $pcCategory2MasterOptions = [];
    public $pcCategory3MasterOptions = [];
    public $selectedPcCategory1MasterId = '';
    public $selectedPcCategory2MasterId = '';
    public $selectedPcCategory3MasterId = '';
    public $commonCategory2MasterOptions = [];
    public $commonCategory3MasterOptions = [];
    public $selectedCommonCategory2MasterId = '';
    public $selectedCommonCategory3MasterId = '';
    public $investmentSearchKeyword = '';
    public $tradingCompanyRanks = null;

    public function mount()
    {
        $this->loadPcCategory1MasterOptions();
        $this->loadPcCategory2MasterOptions();
        $this->loadPcCategory3MasterOptions();
        $this->loadCommonCategory2MasterOptions();
        $this->loadCommonCategory3MasterOptions();
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

    public function updatedSelectedCommonCategory2MasterId()
    {
        $this->loadCommonCategory3MasterOptions();
        if (!$this->optionContainsValue($this->selectedCommonCategory3MasterId, $this->commonCategory3MasterOptions)) {
            $this->selectedCommonCategory3MasterId = '';
        }

        $this->dispatchCommonCategory3MasterOptions();
        $this->loadTradingCompanyRanks();
    }

    public function updatedSelectedCommonCategory3MasterId()
    {
        $this->loadTradingCompanyRanks();
    }

    public function updatedInvestmentSearchKeyword()
    {
        $this->loadTradingCompanyRanks();
    }

    private function loadPcCategory1MasterOptions(): void
    {
        $this->pcCategory1MasterOptions = Category1Master::query()
            ->where('id', '!=', Category1Master::EQUIPTMENT)
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

    private function loadCommonCategory2MasterOptions(): void
    {
        $this->commonCategory2MasterOptions = Category2Master::query()
            ->where('category1_master_id', Category1Master::EQUIPTMENT)
            ->orderBy('disp_rank', 'asc')
            ->orderBy('id', 'asc')
            ->pluck('item_name', 'id')
            ->toArray();
    }

    private function loadCommonCategory3MasterOptions(): void
    {
        if ($this->selectedCommonCategory2MasterId === '' || $this->selectedCommonCategory2MasterId === null) {
            $this->commonCategory3MasterOptions = [];
            return;
        }

        $this->commonCategory3MasterOptions = Category3Master::query()
            ->where('category2_master_id', $this->selectedCommonCategory2MasterId)
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

    private function dispatchCommonCategory3MasterOptions(): void
    {
        $this->dispatch(
            'select-search-options',
            name: 'common_category3_master_id',
            options: $this->commonCategory3MasterOptions,
            value: $this->selectedCommonCategory3MasterId === null ? '' : (string) $this->selectedCommonCategory3MasterId,
        );
    }

    private function loadTradingCompanyRanks(): void
    {
        $searchKeyword = trim((string) $this->investmentSearchKeyword);
        $hasPcCategoryFilter =
            ($this->selectedPcCategory1MasterId !== '' && $this->selectedPcCategory1MasterId !== null) ||
            ($this->selectedPcCategory2MasterId !== '' && $this->selectedPcCategory2MasterId !== null) ||
            ($this->selectedPcCategory3MasterId !== '' && $this->selectedPcCategory3MasterId !== null);
        $hasCommonCategoryFilter =
            ($this->selectedCommonCategory2MasterId !== '' && $this->selectedCommonCategory2MasterId !== null) ||
            ($this->selectedCommonCategory3MasterId !== '' && $this->selectedCommonCategory3MasterId !== null);
        $hasCategoryFilter =
            $hasPcCategoryFilter ||
            $hasCommonCategoryFilter;
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
            ])
            ->where('deleted', 0);

        if ($hasPcCategoryFilter) {
            $pcExistsQuery = TradingCompanyRank::query()
                ->select('trading_company_id')
                ->where('deleted', 0);
            $this->applyPcCategoryFilters($pcExistsQuery);

            $query->whereIn('trading_company_id', $pcExistsQuery);
        }

        if ($hasCommonCategoryFilter) {
            $commonExistsQuery = TradingCompanyRank::query()
                ->select('trading_company_id')
                ->where('deleted', 0);
            $this->applyCommonCategoryFilters($commonExistsQuery);

            $query->whereIn('trading_company_id', $commonExistsQuery);
        }

        if ($hasCategoryFilter) {
            $query->where(function ($categoryQuery) use ($hasPcCategoryFilter, $hasCommonCategoryFilter) {
                if ($hasPcCategoryFilter) {
                    $categoryQuery->where(function ($pcQuery) {
                        $this->applyPcCategoryFilters($pcQuery);
                    });
                }

                if ($hasCommonCategoryFilter) {
                    $method = $hasPcCategoryFilter ? 'orWhere' : 'where';
                    $categoryQuery->{$method}(function ($commonQuery) {
                        $this->applyCommonCategoryFilters($commonQuery);
                    });
                }
            });
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

        $tradingCompanyRanks = $query
            ->orderByRaw('rank IS NULL, rank ASC')
            ->orderBy('id', 'asc')
            ->get();

        $this->tradingCompanyRanks = $tradingCompanyRanks
            ->groupBy('trading_company_id')
            ->map(function ($ranks) {
                $firstRank = $ranks->first();

                $category2Names = $ranks
                    ->map(function ($rank) {
                        return $rank->category2Master?->item_name;
                    })
                    ->filter()
                    ->unique()
                    ->values();

                $category3Names = $ranks
                    ->map(function ($rank) {
                        return $rank->category3Master?->item_name;
                    })
                    ->filter()
                    ->unique()
                    ->values();

                return (object) [
                    'trading_company_id' => $firstRank->trading_company_id,
                    'tradingCompany' => $firstRank->tradingCompany,
                    'category2_names' => $category2Names,
                    'category3_names' => $category3Names,
                ];
            })
            ->values();
    }

    private function applyPcCategoryFilters($query): void
    {
        if ($this->selectedPcCategory1MasterId !== '' && $this->selectedPcCategory1MasterId !== null) {
            $query->where('category1_master_id', $this->selectedPcCategory1MasterId);
        }
        if ($this->selectedPcCategory2MasterId !== '' && $this->selectedPcCategory2MasterId !== null) {
            $query->where('category2_master_id', $this->selectedPcCategory2MasterId);
        }
        if ($this->selectedPcCategory3MasterId !== '' && $this->selectedPcCategory3MasterId !== null) {
            $query->where('category3_master_id', $this->selectedPcCategory3MasterId);
        }
    }

    private function applyCommonCategoryFilters($query): void
    {
        $query->where('category1_master_id', Category1Master::EQUIPTMENT);

        if ($this->selectedCommonCategory2MasterId !== '' && $this->selectedCommonCategory2MasterId !== null) {
            $query->where('category2_master_id', $this->selectedCommonCategory2MasterId);
        }
        if ($this->selectedCommonCategory3MasterId !== '' && $this->selectedCommonCategory3MasterId !== null) {
            $query->where('category3_master_id', $this->selectedCommonCategory3MasterId);
        }
    }

    public function render()
    {
        return view('livewire.admin.repair-map.map-list');
    }
}
