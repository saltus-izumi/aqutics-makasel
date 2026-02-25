<?php

namespace App\Livewire\Admin\Progress\Ge;

use App\Models\GeProgress;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class ProgressList extends Component
{
    public $conditions = [];
    public $geProgresses = null;
    public bool $incompleteOnly = true;
    public string $searchKeyword = '';
    public string $sortOrder = 'asc';
    public string $sortField = 'progress_id';
    public array $filters = [];
    public array $genpukuResponsibleOptions = [];
    public array $genpukuResponsibleShortOptions = [];
    public array $nextActionOptions = [];
    public array $averageLt = [];

    protected array $progressMap = [
        'responsible_user_id' => 'genpuku_responsible_id',
        'move_out_received_date' => 'taikyo_uketuke_date',
        'move_out_date' => 'taikyo_date',
        'cost_received_date' => 'genpuku_mitsumori_recieved_date',
        'power_activation_date' => 'tsuden',
        'tenant_burden_confirmed_date' => 'tenant_charge_confirmed_date',
        'owner_proposed_date' => 'genpuku_teian_date',
        'owner_approved_date' => 'genpuku_teian_kyodaku_date',
        'ordered_date' => 'genpuku_kouji_hachu_date',
        'completion_scheduled_date' => 'kanko_yotei_date',
        'completion_received_date' => 'kanko_jyushin_date',
        'completion_reported_date' => 'owner_kanko_houkoku_date',
        'kakumei_registered_date' => 'kakumei_koujo_touroku_date',
        'completed_date' => 'ge_complete_date',
    ];

    public function mount()
    {
        $this->incompleteOnly = true;
        $this->genpukuResponsibleOptions = User::getOptions(User::DEPARTMENT_GE);
        $this->genpukuResponsibleShortOptions = User::getShortOptions(User::DEPARTMENT_GE);
        $this->nextActionOptions = GeProgress::NEXT_ACTIONS;

        // フィルター初期値
        $this->filters = $this->normalizeFilters([
            // 'ge_complete_date' => [
            //     'value' => '',
            //     'blank' => 'blank',
            // ],
        ]);

        $this->refreshGeProgresses();
    }

    public function render()
    {
        return view('livewire.admin.progress.ge.progress-list');
    }

    protected function refreshGeProgresses() {
        $query = GeProgress::query()
            ->with([
                'lowerEstimateFiles',
                'progress',
                'progress.investment',
                'progress.investment.landlord.owner',
                'progress.investmentRoom',
                'progress.investmentEmptyRoom',
            ]);

        $query = $this->setOrder($query);

        $query = $this->setCondition($query);

        $this->geProgresses = $query->get();
        $this->averageLt = $this->buildAverageLt($this->geProgresses);
    }

    protected function buildAverageLt($progresses): array
    {
        $pairs = [
            'cancellation' => ['move_out_received_date', 'progress.investmentEmptyRoom.cancellation_date'],
            'move_out' => ['investmentEmptyRoom.cancellation_date', 'move_out_date'],
            'cost_received' => ['move_out_date', 'cost_received_date'],
            // 'tsuden' => ['cost_received_date', 'tenant_charge_confirmed_date'],
            'tenant_burden_confirmed' => ['cost_received_date', 'tenant_burden_confirmed_date'],
            'owner_proposed' => ['tenant_burden_confirmed_date', 'owner_proposed_date'],
            'owner_approved' => ['owner_proposed_date', 'owner_approved_date'],
            'ordered' => ['owner_approved_date', 'ordered_date'],
            'completion_scheduled' => ['ordered_date', 'completion_scheduled_date'],
            'completion_received' => ['completion_scheduled_date', 'completion_received_date'],
            'completion_reported' => ['completion_received_date', 'completion_reported_date'],
            'kakumei_registered' => ['completion_reported_date', 'kakumei_registered_date'],
            'complete' => ['kakumei_registered_date', 'completed_date'],
        ];

        $averages = $this->averageDaysBatch($progresses, $pairs);
        $labels = [];
        foreach ($pairs as $key => $_pair) {
            $labels[$key] = $averages[$key] === null ? 'ー' : ($averages[$key] . '日');
        }

        return $labels;
    }

    protected function averageDaysBatch($progresses, array $pairs): array
    {
        $totals = [];
        $counts = [];
        foreach ($pairs as $key => $_pair) {
            $totals[$key] = 0;
            $counts[$key] = 0;
        }

        foreach ($progresses as $progress) {
            foreach ($pairs as $key => [$startPath, $endPath]) {
                $start = $this->normalizeDate(data_get($progress, $startPath));
                $end = $this->normalizeDate(data_get($progress, $endPath));
                if (!$start || !$end) {
                    continue;
                }
                $totals[$key] += $start->diffInDays($end);
                $counts[$key]++;
            }
        }

        $averages = [];
        foreach ($pairs as $key => $_pair) {
            if ($counts[$key] === 0) {
                $averages[$key] = null;
                continue;
            }
            $averages[$key] = (int) round($totals[$key] / $counts[$key]);
        }

        return $averages;
    }

    protected function normalizeDate($value): ?CarbonInterface
    {
        if (!$value) {
            return null;
        }
        if ($value instanceof CarbonInterface) {
            return $value;
        }

        return Carbon::parse($value);
    }

    public function updateDate($progressId, $field, $date)
    {
        DB::transaction(function() use ($progressId, $field, $date) {
            $geProgress = GeProgress::query()
                ->with([
                    'progress',
                    'progress.investment',
                    'progress.investmentRoom',
                    'progress.investmentEmptyRoom',
                ])
                ->find($progressId);

            if (!$geProgress) {
                return;
            }

            if ($date == 'ー') {
                $geProgress->{$field} = null;
                $geProgress->{$field . '_state'} = 2;
            }
            elseif ($date == '') {
                $geProgress->{$field} = $date;
                $geProgress->{$field . '_state'} = 0;
            }
            else {
                $geProgress->{$field} = $date;
                $geProgress->{$field . '_state'} = 1;
            }

            $geProgress->resetNextAction();
            $geProgress->save();

            if (array_key_exists($field, $this->progressMap)) {
                $progressField = $this->progressMap[$field];
                $this->geProgress->progress->{$progressField} = $date;
                $this->geProgress->progress->save();
            }
        });

        $this->refreshGeProgresses();
    }

    public function updateSelectValue($progressId, $field, $id) {
        DB::transaction(function() use ($progressId, $field, $id) {
            $id = empty($id) ? null : $id;

            $geProgress = GeProgress::query()
                ->with([
                    'progress',
                    'progress.investment',
                    'progress.investmentRoom',
                    'progress.investmentEmptyRoom',
                ])
                ->find($progressId);

            $geProgress->{$field} = $id;
            $geProgress->save();
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
        Log::debug('sortField=' . $this->sortField);
        $this->sortOrder = $this->normalizeSortOrder($sortOrder);
        $this->sortField = $sortField;
        $this->filters = $this->normalizeFilters($filters);
        $this->refreshGeProgresses();
    }

    protected function setCondition($query)
    {
        if ($this->incompleteOnly) {
            $query->whereNull('completed_date');
            $query->whereNull('cancellation_reversed_date');
        }

        if ($this->searchKeyword !== '') {
            $keyword = $this->searchKeyword;
            $query->where(function ($q) use ($keyword) {
                $q
                    ->orWhereHas('progress.investment', function ($q) use ($keyword) {
                        $q->where('investment_name', 'like', '%' . $keyword . '%')
                            ->orWhere('id', $keyword);
                    })
                    ->orWhereHas('progress.investment.landlord.owner', function ($q) use ($keyword) {
                        $q->where('name', 'like', '%' . $keyword . '%')
                            ->orWhere('id', $keyword);
                    });
            });
        }

        foreach ($this->filters as $filterField => $filter) {
            $rawValue = $filter['value'] ?? '';
            $filterValue = is_array($rawValue) ? $this->normalizeDateRangeValue($rawValue) : trim((string) $rawValue);
            $filterBlank = $this->normalizeFilterBlank($filter['blank'] ?? '');
            if ($filterValue === '' && $filterBlank === '') {
                continue;
            }

            if (in_array($filterField, $this->getDateRangeFilterFields(), true)) {
                $rangeValue = is_array($filterValue) ? $filterValue : ['from' => '', 'to' => ''];
                $this->applyDateRangeFilter($query, $filterField, $rangeValue, $filterBlank);
                continue;
            }
            if (array_key_exists($filterField, $this->getDateRangeRelationFields())) {
                $relation = $this->getDateRangeRelationFields()[$filterField]['relation'];
                $column = $this->getDateRangeRelationFields()[$filterField]['column'];
                $rangeValue = is_array($filterValue) ? $filterValue : ['from' => '', 'to' => ''];
                $this->applyRelationDateRangeFilter($query, $relation, $column, $rangeValue, $filterBlank);
                continue;
            }

            if ($filterField === 'investment_id') {
                $this->applyRelationFilter($query, 'progress', 'investment_id', $filterValue, $filterBlank, true);
            } elseif ($filterField === 'investment_name') {
                $this->applyRelationFilter($query, 'progress.investment', 'investment_name', $filterValue, $filterBlank, true);
            } elseif ($filterField === 'investment_room_number') {
                if ($filterValue !== '') {
                    if ($filterValue === '共用部') {
                        $query->where('investment_room_number', 0);
                    } else {
                        $this->applyRelationFilter($query, 'progress.investmentRoom', 'investment_room_number', $filterValue, $filterBlank);
                    }
                } else {
                    $this->applyRelationFilter($query, 'progress.investmentRoom', 'investment_room_number', $filterValue, $filterBlank);
                }
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

    // dump($sortField);
        if ($sortField === 'investment_id') {
            $query->leftJoin('progresses as sort_progresses', 'sort_progresses.id', '=', 'ge_progresses.progress_id')
                ->select('ge_progresses.*')
                ->orderBy('sort_progresses.investment_id', $sortOrder);
        } elseif ($sortField === 'investment_name') {
            $query->leftJoin('progresses as sort_progresses', 'sort_progresses.id', '=', 'ge_progresses.progress_id')
                ->leftJoin('investments as sort_investments', 'sort_investments.id', '=', 'sort_progresses.investment_id')
                ->select('ge_progresses.*')
                ->orderBy('sort_investments.investment_name', $sortOrder);
        } elseif ($sortField === 'investment_room_number') {
            $query->leftJoin('progresses as sort_progresses', 'sort_progresses.id', '=', 'ge_progresses.progress_id')
                ->leftJoin('investment_rooms as sort_investment_rooms', 'sort_investment_rooms.id', '=', 'sort_progresses.investment_room_uid')
                ->select('ge_progresses.*')
                ->orderBy('sort_investment_rooms.investment_room_number', $sortOrder);
        } elseif ($sortField === 'executor_user_id') {
            $query->orderBy('executor_user_id', $sortOrder);
        } elseif ($sortField === 'next_action') {
            $query->orderBy('next_action', $sortOrder);
        } elseif ($sortField === 'cancellation_date') {
            $query->leftJoin('progresses as sort_progresses', 'sort_progresses.id', '=', 'ge_progresses.progress_id')
                ->leftJoin('investment_empty_rooms as sort_investment_empty_rooms', 'sort_investment_empty_rooms.id', '=', 'sort_progresses.investment_empty_room_id')
                ->select('ge_progresses.*')
                ->orderBy('sort_investment_empty_rooms.cancellation_date', $sortOrder);
        } else {
            $query->orderBy($sortField, $sortOrder);
        }

        if ($sortField !== 'id') {
            $query->orderBy('ge_progresses.id', $sortOrder);
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
        $rawValue = $filter['value'] ?? '';
        if (is_array($rawValue)) {
            $from = trim((string) ($rawValue['from'] ?? ''));
            $to = trim((string) ($rawValue['to'] ?? ''));
            $value = $from !== '' || $to !== '';
        } else {
            $value = trim((string) $rawValue) !== '';
        }
        $blank = $this->normalizeFilterBlank($filter['blank'] ?? '');
        return $value || $blank !== '';
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
            $valueRaw = $filter['value'] ?? '';
            if (is_object($valueRaw)) {
                $valueRaw = (array) $valueRaw;
            }
            if (is_array($valueRaw)) {
                if (
                    !in_array($field, $this->getDateRangeFilterFields(), true)
                    && !array_key_exists($field, $this->getDateRangeRelationFields())
                ) {
                    continue;
                }
                $value = $this->normalizeDateRangeValue($valueRaw);
            } else {
                $value = trim((string) $valueRaw);
            }
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
            ...$this->getDateRangeFilterFields(),
            ...array_keys($this->getDateRangeRelationFields()),
        ]);
    }

    protected function getSimpleFilterFields()
    {
        return [
            'id',
            'progress_id',
            'investment_id',
            'genpuku_responsible_id',
        ];
    }

    protected function getDateRangeFilterFields()
    {
        return [
            'move_out_received_date',               // 退去受付日
            'move_out_date',                        // 退去日
            'cost_received_date',                   // 下代受信日
            'power_activation_date',                // 通電日
            'tenant_burden_confirmed_date',         // 借主負担確定日
            'owner_proposed_date',                  // 貸主提案日
            'owner_approved_date',                  // 貸主承諾日
            'ordered_date',                         // 発注日
            'completion_scheduled_date',            // 完工予定日
            'completion_received_date',             // 完工受信日
            'completion_reported_date',             // 完工報告日
            'kakumei_registered_date',              // 革命控除登録日
            'completed_date',                       // 完了日
        ];
    }

    protected function getDateRangeRelationFields()
    {
        return [
            'cancellation_date' => [
                'relation' => 'progress.investmentEmptyRoom',
                'column' => 'cancellation_date',
            ],
        ];
    }

    protected function normalizeDateRangeValue($value)
    {
        $from = trim((string) ($value['from'] ?? ''));
        $to = trim((string) ($value['to'] ?? ''));
        if ($from === '' && $to === '') {
            return '';
        }
        return [
            'from' => $from,
            'to' => $to,
        ];
    }

    protected function applyDateRangeFilter($query, $column, $filterValue, $filterBlank)
    {
        $from = $filterValue['from'] ?? '';
        $to = $filterValue['to'] ?? '';
        if ($from !== '' || $to !== '') {
            if ($from !== '' && $to !== '') {
                $query->whereDate($column, '>=', $from)
                    ->whereDate($column, '<=', $to);
                return;
            }
            if ($from !== '') {
                $query->whereDate($column, '>=', $from);
                return;
            }
            $query->whereDate($column, '<=', $to);
            return;
        }
        if ($filterBlank === 'blank') {
            $query->where(function ($q) use ($column) {
                $q->whereNull($column)
                    ->orWhere($column, '');
            });
        } elseif ($filterBlank === 'not_blank') {
            $query->whereNotNull($column)
                ->where($column, '!=', '');
        }
    }

    protected function applyRelationDateRangeFilter($query, $relation, $column, $filterValue, $filterBlank)
    {
// dd($filterBlank);
        $from = $filterValue['from'] ?? '';
        $to = $filterValue['to'] ?? '';
        if ($from !== '' || $to !== '') {
            $query->whereHas($relation, function ($q) use ($column, $from, $to) {
                if ($from !== '' && $to !== '') {
                    $q->whereDate($column, '>=', $from)
                        ->whereDate($column, '<=', $to);
                    return;
                }
                if ($from !== '') {
                    $q->whereDate($column, '>=', $from);
                    return;
                }
                $q->whereDate($column, '<=', $to);
            });
            return;
        }
        if ($filterBlank === 'blank') {
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
}
