<?php

namespace App\Livewire\Admin\GeProgress;

use App\Models\GeProgress;
use App\Models\Investment;
use App\Models\InvestmentRoom;
use App\Models\Progress;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
    public array $filters = [];
    public array $genpukuResponsibleOptions = [];
    public array $nextActionOptions = [];

    public function mount()
    {
        $this->incompleteOnly = true;
        $this->genpukuResponsibleOptions = User::getOptions(User::DEPARTMENT_GE);
        $this->refreshGeProgresses();
        $this->nextActionOptions = GeProgress::NEXT_ACTIONS;
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

        $query = $this->setOrder($query);

        $query = $this->setCondition($query);

        $this->progresses = $query->get();
    }

    public function updateDate($progressId, $field, $date)
    {
        DB::transaction(function() use ($progressId, $field, $date) {
            $progress = Progress::query()
                ->with([
                    'investment',
                    'investmentRoom',
                    'investmentEmptyRoom',
                    'GeProgress',
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

            $progress->geProgress->next_action = $progress->ge_next_action;
            $progress->geProgress->save();
        });

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

    public function updateSortFilter($sortOrder, $sortField, $filters)
    {
        $this->sortOrder = $this->normalizeSortOrder($sortOrder);
        $this->sortField = $sortField;
        $this->filters = $this->normalizeFilters($filters);
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

        foreach ($this->filters as $filterField => $filter) {
            $filterValue = trim((string) ($filter['value'] ?? ''));
            $filterBlank = $this->normalizeFilterBlank($filter['blank'] ?? '');
            if ($filterValue === '' && $filterBlank === '') {
                continue;
            }

            if ($filterField === 'investment_name') {
                $this->applyRelationFilter($query, 'investment', 'investment_name', $filterValue, $filterBlank, true);
            } elseif ($filterField === 'investment_room_number') {
                if ($filterValue !== '') {
                    if ($filterValue === '共用部') {
                        $query->where('investment_room_number', 0);
                    } else {
                        $this->applyRelationFilter($query, 'investmentRoom', 'investment_room_number', $filterValue, $filterBlank);
                    }
                } else {
                    $this->applyRelationFilter($query, 'investmentRoom', 'investment_room_number', $filterValue, $filterBlank);
                }
            } elseif ($filterField === 'executor_user_id') {
                $this->applyRelationFilter($query, 'geProgress', 'executor_user_id', $filterValue, $filterBlank);
            } elseif ($filterField === 'next_action') {
                $this->applyRelationFilter($query, 'geProgress', 'next_action', $filterValue, $filterBlank);
            } elseif (in_array($filterField, $this->getSimpleFilterFields(), true)) {
                $this->applyColumnFilter($query, $filterField, $filterValue, $filterBlank);
            }
        }

        return $query;
    }

    protected function setOrder($query)
    {
        $sortOrder = $this->normalizeSortOrder($this->sortOrder);
        $sortField = $this->sortField;
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
        } elseif ($sortField === 'executor_user_id') {
            $query->orderBy(
                GeProgress::select('executor_user_id')
                    ->whereColumn('ge_progresses.progress_id', 'progresses.id'),
                $sortOrder
            );
        } elseif ($sortField === 'next_action') {
            $query->orderBy(
                GeProgress::select('next_action')
                    ->whereColumn('ge_progresses.progress_id', 'progresses.id'),
                $sortOrder
            );
        } else {
            $query->orderBy($sortField, $sortOrder);
        }

        return $query;
    }

    protected function normalizeSortOrder($sortOrder)
    {
        return $sortOrder === 'desc' ? 'desc' : 'asc';
    }

    protected function normalizeFilterBlank($value)
    {
        return in_array($value, ['blank', 'not_blank'], true) ? $value : '';
    }

    protected function applyColumnFilter($query, $column, $filterValue, $filterBlank)
    {
        if ($filterValue !== '') {
            $query->where($column, $filterValue);
        } elseif ($filterBlank === 'blank') {
            $query->whereNull($column);
        } elseif ($filterBlank === 'not_blank') {
            $query->whereNotNull($column);
        }
    }

    protected function applyRelationFilter($query, $relation, $column, $filterValue, $filterBlank, $like = false)
    {
        if ($filterValue !== '') {
            $query->whereHas($relation, function ($q) use ($column, $filterValue, $like) {
                if ($like) {
                    $q->where($column, 'like', '%' . $filterValue . '%');
                } else {
                    $q->where($column, $filterValue);
                }
            });
        } elseif ($filterBlank === 'blank') {
            $query->whereHas($relation, function ($q) use ($column) {
                $q->whereNull($column)
                    ->orWhere($column, '');
            });
        } elseif ($filterBlank === 'not_blank') {
            $query->whereHas($relation, function ($q) use ($column) {
                $q->whereNotNull($column)
                    ->where($column, '!=', '');
            });
        }
    }

    public function hasFilter($field)
    {
        if (!is_string($field) || $field === '') {
            return false;
        }
        $filter = $this->filters[$field] ?? null;
        if (!is_array($filter)) {
            return false;
        }
        $value = trim((string) ($filter['value'] ?? ''));
        $blank = $this->normalizeFilterBlank($filter['blank'] ?? '');
        return $value !== '' || $blank !== '';
    }

    protected function normalizeFilters($filters)
    {
        if (is_object($filters)) {
            $filters = (array) $filters;
        }
        if (!is_array($filters)) {
            return [];
        }

        $normalized = [];
        foreach ($filters as $field => $filter) {
            if (!is_string($field)) {
                continue;
            }
            if (is_object($filter)) {
                $filter = (array) $filter;
            }
            if (!is_array($filter)) {
                continue;
            }
            $value = trim((string) ($filter['value'] ?? ''));
            $blank = $this->normalizeFilterBlank($filter['blank'] ?? '');
            if ($value === '' && $blank === '') {
                continue;
            }
            if (!in_array($field, $this->getAllowedFilterFields(), true)) {
                continue;
            }
            $normalized[$field] = [
                'value' => $value,
                'blank' => $blank,
            ];
        }

        return $normalized;
    }

    protected function getAllowedFilterFields()
    {
        return array_merge($this->getSimpleFilterFields(), [
            'investment_name',
            'investment_room_number',
            'executor_user_id',
            'next_action',
        ]);
    }

    protected function getSimpleFilterFields()
    {
        return [
            'id',
            'investment_id',
            'genpuku_responsible_id',
        ];
    }
}
