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
Log::debug($filters);
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
                if ($filterValue !== '') {
                    $query->whereHas('investment', function ($q) use ($filterValue) {
                        $q->where('investment_name', 'like', '%' . $filterValue . '%');
                    });
                } elseif ($filterBlank === 'blank') {
                    $query->whereHas('investment', function ($q) {
                        $q->whereNull('investment_name')
                            ->orWhere('investment_name', '');
                    });
                } elseif ($filterBlank === 'not_blank') {
                    $query->whereHas('investment', function ($q) {
                        $q->whereNotNull('investment_name')
                            ->where('investment_name', '!=', '');
                    });
                }
            } elseif ($filterField === 'investment_room_number') {
                if ($filterValue !== '') {
                    if ($filterValue === '共用部') {
                        $query->where('investment_room_number', 0);
                    } else {
                        $query->whereHas('investmentRoom', function ($q) use ($filterValue) {
                            $q->where('investment_room_number', $filterValue);
                        });
                    }
                } elseif ($filterBlank === 'blank') {
                    $query->whereHas('investmentRoom', function ($q) {
                        $q->whereNull('investment_room_number')
                            ->orWhere('investment_room_number', '');
                    });
                } elseif ($filterBlank === 'not_blank') {
                    $query->whereHas('investmentRoom', function ($q) {
                        $q->whereNotNull('investment_room_number')
                            ->where('investment_room_number', '!=', '');
                    });
                }
            } elseif ($filterField === 'executor_user_id') {
                if ($filterValue !== '') {
                    $query->whereHas('geProgress', function ($q) use ($filterValue) {
                        $q->where('executor_user_id', $filterValue);
                    });
                } elseif ($filterBlank === 'blank') {
                    $query->whereHas('geProgress', function ($q) {
                        $q->whereNull('executor_user_id')
                            ->orWhere('executor_user_id', '');
                    });
                } elseif ($filterBlank === 'not_blank') {
                    $query->whereHas('geProgress', function ($q) {
                        $q->whereNotNull('executor_user_id')
                            ->where('executor_user_id', '!=', '');
                    });
                }
            } elseif ($filterField === 'next_action') {
                if ($filterValue !== '') {
                    $query->whereHas('geProgress', function ($q) use ($filterValue) {
                        $q->where('next_action', $filterValue);
                    });
                } elseif ($filterBlank === 'blank') {
                    $query->whereHas('geProgress', function ($q) {
                        $q->whereNull('next_action')
                            ->orWhere('next_action', '');
                    });
                } elseif ($filterBlank === 'not_blank') {
                    $query->whereHas('geProgress', function ($q) {
                        $q->whereNotNull('next_action')
                            ->where('next_action', '!=', '');
                    });
                }
            } elseif (in_array($filterField, $this->getSimpleFilterFields(), true)) {
                if ($filterValue !== '') {
                    $query->where($filterField, $filterValue);
                } elseif ($filterBlank === 'blank') {
                    $query->whereNull($filterField);
                } elseif ($filterBlank === 'not_blank') {
                    $query->whereNotNull($filterField);
                }
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

    protected function normalizeFilters($filters)
    {
        if (!is_array($filters)) {
            return [];
        }

        $normalized = [];
        foreach ($filters as $field => $filter) {
            if (!is_string($field)) {
                continue;
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
