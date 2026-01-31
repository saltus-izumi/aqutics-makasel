<?php

namespace App\Livewire\Admin\GeProgress;

use App\Models\Investment;
use App\Models\InvestmentRoom;
use App\Models\Progress;
use Livewire\Attributes\On;
use Livewire\Component;

class ProgressList extends Component
{
    public $conditions = [];
    public $progresses = null;
    public bool $incompleteOnly = true;
    public string $searchKeyword = '';
    public string $sortOrder = 'asc';
    public string $sortField = 'id';
    public string $filterField = 'id';
    public string $filterValue = '';
    public string $filterBlank = '';

    public function mount()
    {
        $this->incompleteOnly = true;
        $this->refreshGeProgresses();
    }

    public function render()
    {
        return view('livewire.admin.ge-progress.progress-list');
    }

    protected function refreshGeProgresses() {
        $query = Progress::query()
            ->with([
                'investment',
                'investment.landlord.owner',
                'investmentRoom',
                'investmentEmptyRoom',
            ])
            ->whereNull('kaiyaku_cancellation_date');

        $sortOrder = $this->normalizeSortOrder($this->sortOrder);
        $sortField = $this->normalizeSortField($this->sortField);
        if ($sortField === 'investment_name') {
            $query->orderBy(
                Investment::select('investment_name')
                    ->whereColumn('investments.id', 'progresses.investment_id'),
                $sortOrder
            );
        } elseif ($sortField === 'investment_room_number') {
            $query->orderBy(
                InvestmentRoom::select('investment_room_number')
                    ->whereColumn('investment_rooms.id', 'progresses.investment_room_uid'),
                $sortOrder
            );
        } else {
            $query->orderBy($sortField, $sortOrder);
        }

        $query = $this->setCondition($query);

        $this->progresses = $query->get();
    }

    public function updateDate($progressId, $field, $date)
    {
        $progress = Progress::query()
            ->with([
                'investment',
                'investmentRoom',
                'investmentEmptyRoom',
            ])
            ->find($progressId);

        if (!$progress) {
            return;
        }

        if ($date == 'ー') {
            $progress->{$field} = null;
            $progress->{$field . '_state'} = 2;
        }
        elseif ($date == '') {
            $progress->{$field} = $date;
            $progress->{$field . '_state'} = 0;
        }
        else {
            $progress->{$field} = $date;
            $progress->{$field . '_state'} = 1;
        }
        $progress->save();
        $this->refreshGeProgresses();
    }

    #[On('ge-progress:incomplete-only-changed')]
    public function updateIncompleteOnly($incompleteOnly)
    {
        $this->incompleteOnly = (bool) $incompleteOnly;
        $this->refreshGeProgresses();
    }

    #[On('ge-progress:search-input-submitted')]
    public function updateSearchKeyword($keyword)
    {
        $this->searchKeyword = trim((string) $keyword);
        $this->refreshGeProgresses();
    }

    public function updateSortFilter($sortOrder, $sortField, $filterField, $filterValue, $filterBlank)
    {
        $this->sortOrder = $this->normalizeSortOrder($sortOrder);
        $this->sortField = $this->normalizeFilterField($sortField);
        $this->filterField = $this->normalizeFilterField($filterField);
        $this->filterValue = trim((string) $filterValue);
        $this->filterBlank = $this->normalizeFilterBlank($filterBlank);
        $this->refreshGeProgresses();
    }

    protected function setCondition($query)
    {
        if ($this->incompleteOnly) {
            $query->whereNull('ge_complete_date');
        }

        if ($this->searchKeyword !== '') {
            $keyword = $this->searchKeyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('investment_id', $keyword)
                    ->orWhereHas('investment', function ($q) use ($keyword) {
                        $q->where('investment_name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhereHas('investment.landlord.owner', function ($q) use ($keyword) {
                        $q->where('name', 'like', '%' . $keyword . '%')
                            ->orWhere('id', $keyword);
                    });
            });
        }

        $filterField = $this->normalizeFilterField($this->filterField);
        if ($this->filterValue !== '') {
            // 物件名
            if ($filterField === 'investment_name') {
                $query->whereHas('investment', function ($q) {
                    $q->where('investment_name', 'like', '%' . $this->filterValue . '%');
                });
            // 号室
            } elseif ($filterField === 'investment_room_number') {
                if ($this->filterValue === '共用部') {
                    $query->where('investment_room_number', 0);
                } else {
                    $query->whereHas('investmentRoom', function ($q) {
                        $q->where('investment_room_number', $this->filterValue);
                    });
                }
            // その他の項目
            } else {
                $query->where($filterField, $this->filterValue);
            }
        } elseif ($this->filterBlank === 'blank') {
            if ($filterField === 'investment_name') {
                $query->whereHas('investment', function ($q) {
                    $q->whereNull('investment_name')
                        ->orWhere('investment_name', '');
                });
            } elseif ($filterField === 'investment_room_number') {
                $query->whereHas('investmentRoom', function ($q) {
                    $q->whereNull('investment_room_number')
                        ->orWhere('investment_room_number', '');
                });
            } else {
                $query->whereNull($filterField);
            }
        } elseif ($this->filterBlank === 'not_blank') {
            if ($filterField === 'investment_name') {
                $query->whereHas('investment', function ($q) {
                    $q->whereNotNull('investment_name')
                        ->where('investment_name', '!=', '');
                });
            } elseif ($filterField === 'investment_room_number') {
                $query->whereHas('investmentRoom', function ($q) {
                    $q->whereNotNull('investment_room_number')
                        ->where('investment_room_number', '!=', '');
                });
            } else {
                $query->whereNotNull($filterField);
            }
        }

        return $query;
    }

    protected function normalizeSortOrder($sortOrder)
    {
        return $sortOrder === 'desc' ? 'desc' : 'asc';
    }

    protected function normalizeFilterField($field)
    {
        return in_array($field, ['id', 'investment_id', 'investment_name', 'investment_room_number'], true) ? $field : 'id';
    }

    protected function normalizeSortField($field)
    {
        return in_array($field, ['id', 'investment_id', 'investment_name', 'investment_room_number'], true) ? $field : 'id';
    }

    protected function normalizeFilterBlank($value)
    {
        return in_array($value, ['blank', 'not_blank'], true) ? $value : '';
    }
}
