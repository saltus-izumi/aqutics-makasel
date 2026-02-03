<?php

namespace App\Livewire\Admin\GeProgress;

use App\Models\GeProgress;
use App\Models\Investment;
use App\Models\InvestmentEmptyRoom;
use App\Models\InvestmentRoom;
use App\Models\Progress;
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
    public $progresses = null;
    public bool $incompleteOnly = true;
    public string $searchKeyword = '';
    public string $sortOrder = 'asc';
    public string $sortField = 'id';
    public array $filters = [];
    public array $genpukuResponsibleOptions = [];
    public array $genpukuResponsibleShortOptions = [];
    public array $nextActionOptions = [];
    public array $averageLt = [];
    public int $selectRefreshToken = 0;

    public function mount()
    {
        $this->incompleteOnly = true;
        $this->genpukuResponsibleOptions = User::getOptions(User::DEPARTMENT_GE);
        $this->genpukuResponsibleShortOptions = User::getShortOptions(User::DEPARTMENT_GE);
        $this->nextActionOptions = GeProgress::NEXT_ACTIONS;

        // フィルター初期値
        $this->filters = $this->normalizeFilters([
            'ge_complete_date' => [
                'value' => '',
                'blank' => 'blank',
            ],
        ]);

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

        $query = $this->setOrder($query);

        $query = $this->setCondition($query);

        $this->progresses = $query->get();
        $this->averageLt = $this->buildAverageLt($this->progresses);
        $this->selectRefreshToken++;
    }

    protected function buildAverageLt($progresses): array
    {
        $pairs = [
            'cancellation' => ['taikyo_uketuke_date', 'investmentEmptyRoom.cancellation_date'],
            'taikyo' => ['investmentEmptyRoom.cancellation_date', 'taikyo_date'],
            'genpuku_mitsumori_recieved' => ['taikyo_date', 'genpuku_mitsumori_recieved_date'],
            // 'tsuden' => ['genpuku_mitsumori_recieved_date', 'tenant_charge_confirmed_date'],
            'tenant_charge_confirmed' => ['genpuku_mitsumori_recieved_date', 'tenant_charge_confirmed_date'],
            'genpuku_teian' => ['tenant_charge_confirmed_date', 'genpuku_teian_date'],
            'genpuku_teian_kyodaku' => ['genpuku_teian_date', 'genpuku_teian_kyodaku_date'],
            'genpuku_kouji_hachu' => ['genpuku_teian_kyodaku_date', 'genpuku_kouji_hachu_date'],
            'kanko_yotei' => ['genpuku_kouji_hachu_date', 'kanko_yotei_date'],
            'kanko_jyushin_date' => ['kanko_yotei_date', 'kanko_jyushin_date'],
            'owner_kanko_houkoku' => ['kanko_jyushin_date', 'owner_kanko_houkoku_date'],
            'kakumei_koujo_touroku' => ['owner_kanko_houkoku_date', 'kakumei_koujo_touroku_date'],
            'ge_complete' => ['kakumei_koujo_touroku_date', 'ge_complete_date'],
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

    public function updateSelectValue($progressId, $field, $id) {
        DB::transaction(function() use ($progressId, $field, $id) {
            $id = empty($id) ? null : $id;

            $progress = Progress::query()
                ->with([
                    'investment',
                    'investmentRoom',
                    'investmentEmptyRoom',
                    'GeProgress',
                ])
                ->find($progressId);

            if ($field == 'executor_user_id') {
                $progress->geProgress->{$field} = $id;
                $progress->geProgress->save();
            } else {
                $progress->{$field} = $id;
                $progress->save();
            }
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
        } elseif ($sortField === 'cancellation_date') {
            $query->orderBy(
                InvestmentEmptyRoom::select('cancellation_date')
                    ->whereColumn('investment_empty_rooms.id', 'progresses.investment_empty_room_id'),
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
            'investment_id',
            'genpuku_responsible_id',
        ];
    }

    protected function getDateRangeFilterFields()
    {
        return [
            'ge_application_date',
            'ge_complete_date',
            'genpuku_shiryou_soushin_date',
            'notice_of_intent_to_vacate_date',
            'taikyo_yotei_date',
            'taikyo_date',
            'genpuku_mitsumori_recieved_date',
            'tsuden',
            'tenant_charge_confirmed_date',
            'genpuku_teian_date',
            'genpuku_teian_kyodaku_date',
            'genpuku_kouji_hachu_date',
            'kanko_yotei_date',
            'kanko_jyushin_date',
            'owner_kanko_houkoku_date',
            'kakumei_koujo_touroku_date',
            'taikyo_uketuke_date',
            'kaiyaku_date',
            'last_import_date',
        ];
    }

    protected function getDateRangeRelationFields()
    {
        return [
            'cancellation_date' => [
                'relation' => 'investmentEmptyRoom',
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
