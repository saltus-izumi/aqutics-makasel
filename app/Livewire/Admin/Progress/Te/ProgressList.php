<?php

namespace App\Livewire\Admin\Progress\Te;

use App\Models\Category1Master;
use App\Models\TeProgress;
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
    public $teProgresses = null;
    public bool $incompleteOnly = true;
    public string $searchKeyword = '';
    public string $sortOrder = 'asc';
    public string $sortField = 'id';
    public array $filters = [];
    public array $teResponsibleOptions = [];
    public array $teResponsibleShortOptions = [];
    public array $nextActionOptions = [];
    public array $category1MasterOptions = [];
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
        $this->teResponsibleOptions = User::getOptions(User::DEPARTMENT_TE);
        $this->teResponsibleShortOptions = User::getShortOptions(User::DEPARTMENT_TE);
        $this->nextActionOptions = TeProgress::NEXT_ACTIONS;
        $this->category1MasterOptions = Category1Master::SHORT_NAME;

        // フィルター初期値
        $this->filters = $this->normalizeFilters([
            // 'ge_complete_date' => [
            //     'value' => '',
            //     'blank' => 'blank',
            // ],
        ]);

        $this->refreshTeProgresses();
    }

    public function render()
    {
        return view('livewire.admin.progress.te.progress-list');
    }

    protected function refreshTeProgresses() {
        $query = TeProgress::query()
            ->with([
                'Investment',
                'investmentRoomResidentHistory',
                'investmentRoomResident',
                'tradingCompany1',
                'tradingCompany2',
                'tradingCompany3',
                'category1Master',
                'category2Master',
                'category3Master',
            ]);

        $query = $this->setOrder($query);

        $query = $this->setCondition($query);

        $this->teProgresses = $query->get();
        $this->averageLt = $this->buildAverageLt($this->teProgresses);
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
            $geProgress = TeProgress::query()
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

        $this->refreshTeProgresses();
    }

    public function updateSelectValue($progressId, $field, $id) {
        DB::transaction(function() use ($progressId, $field, $id) {
            $id = empty($id) ? null : $id;

            $geProgress = TeProgress::query()
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

        $this->refreshTeProgresses();
    }


    #[On('ge-progress:incomplete-only-changed')]
    public function updateIncompleteOnly($incompleteOnly)
    {
        $this->incompleteOnly = (bool) $incompleteOnly;
        $this->refreshTeProgresses();
    }

    #[On('ge-progress:search-input-submitted')]
    public function updateSearchKeyword($keyword)
    {
        $this->searchKeyword = trim((string) $keyword);
        $this->refreshTeProgresses();
    }

    public function updateSortFilter($sortOrder, $sortField, $filters)
    {
        Log::debug('sortField=' . $this->sortField);
        $this->sortOrder = $this->normalizeSortOrder($sortOrder);
        $this->sortField = $sortField;
        $this->filters = $this->normalizeFilters($filters);
        $this->refreshTeProgresses();
    }

    protected function setCondition($query)
    {
        if ($this->incompleteOnly) {
            $query->whereNull('complete_date');
        }

        if ($this->searchKeyword !== '') {
            $keyword = $this->searchKeyword;
            $query->where(function ($q) use ($keyword) {
                $q
                    ->orWhereHas('investment', function ($q) use ($keyword) {
                        $q->where('investment_name', 'like', '%' . $keyword . '%')
                            ->orWhere('id', $keyword);
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

            if ($filterField === 'ansin_support') {
                $this->applyAnsinSupportFilter($query, $filterValue, $filterBlank);
                continue;
            }

            if (array_key_exists($filterField, $this->getBooleanRelationFilterMap())) {
                $column = $this->getBooleanRelationFilterMap()[$filterField];
                $this->applyInvestmentBooleanFilter($query, $column, $filterValue, $filterBlank);
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
                $this->applyColumnFilter($query, 'investment_id', $filterValue, $filterBlank);
            } elseif ($filterField === 'investment_name') {
                $this->applyRelationFilter($query, 'investment', 'investment_name', $filterValue, $filterBlank, true);
            } elseif ($filterField === 'investment_room_number') {
                if ($filterValue !== '') {
                    if ($filterValue === '共用部') {
                        $query->where('investment_room_uid', 0);
                    } else {
                        $this->applyRelationFilter($query, 'investmentRoom', 'investment_room_number', $filterValue, $filterBlank, true);
                    }
                } else {
                    $this->applyRelationFilter($query, 'investmentRoom', 'investment_room_number', $filterValue, $filterBlank, true);
                }
            } elseif ($filterField === 'contractor_name') {
                $this->applyRelationFilter($query, 'investmentRoomResidentHistory', 'contractor_name', $filterValue, $filterBlank, true);
            } elseif ($filterField === 'category2_master') {
                $this->applyRelationFilter($query, 'category2Master', 'item_name', $filterValue, $filterBlank, true);
            } elseif ($filterField === 'category3_master') {
                $this->applyRelationFilter($query, 'category3Master', 'item_name', $filterValue, $filterBlank, true);
            } elseif ($filterField === 'genpuku_gyousha_id') {
                $this->applyTradingCompanyFilter($query, $filterValue, $filterBlank);
            } elseif (in_array($filterField, $this->getLikeFilterFields(), true)) {
                $this->applyColumnFilterLike($query, $filterField, $filterValue, $filterBlank);
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

        if ($sortField === 'investment_id') {
            $query->orderBy('investment_id', $sortOrder);
        } elseif ($sortField === 'investment_name') {
            $query->leftJoin('investments as sort_investments', 'sort_investments.id', '=', 'te_progresses.investment_id')
                ->select('te_progresses.*')
                ->orderBy('sort_investments.investment_name', $sortOrder);
        } elseif ($sortField === 'investment_room_number') {
            $query->leftJoin('investment_rooms as sort_investment_rooms', 'sort_investment_rooms.id', '=', 'te_progresses.investment_room_uid')
                ->select('te_progresses.*')
                ->orderBy('sort_investment_rooms.investment_room_number', $sortOrder);
        } elseif ($sortField === 'contractor_name') {
            $query->leftJoin('investment_room_resident_histories as sort_resident_histories', 'sort_resident_histories.contractor_no', '=', 'te_progresses.contractor_no')
                ->select('te_progresses.*')
                ->orderBy('sort_resident_histories.contractor_name', $sortOrder);
        } elseif ($sortField === 'category2_master') {
            $query->leftJoin('category2_masters as sort_category2_masters', 'sort_category2_masters.id', '=', 'te_progresses.category2_master_id')
                ->select('te_progresses.*')
                ->orderBy('sort_category2_masters.item_name', $sortOrder);
        } elseif ($sortField === 'category3_master') {
            $query->leftJoin('category3_masters as sort_category3_masters', 'sort_category3_masters.id', '=', 'te_progresses.category3_master_id')
                ->select('te_progresses.*')
                ->orderBy('sort_category3_masters.item_name', $sortOrder);
        } elseif ($sortField === 'facility_maintenance' || $sortField === 'three_repair' || $sortField === 'ansin_support') {
            $column = $sortField === 'ansin_support' ? 'has_emergency_support' : $sortField;
            $query->leftJoin('investments as sort_investments', 'sort_investments.id', '=', 'te_progresses.investment_id')
                ->select('te_progresses.*')
                ->orderBy('sort_investments.' . $column, $sortOrder);
        } elseif ($sortField === 'genpuku_gyousha_id') {
            $query->orderBy('trading_company_1_id', $sortOrder);
        } else {
            $query->orderBy($sortField, $sortOrder);
        }

        if ($sortField !== 'id') {
            $query->orderBy('te_progresses.id', $sortOrder);
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

    protected function applyColumnFilterLike($query, $column, $filterValue, $filterBlank)
    {
        if ($filterValue !== '') {
            $query->where($column, 'like', '%' . $filterValue . '%');
        } elseif ($filterBlank === 'blank') {
            $query->where(function ($q) use ($column) {
                $q->whereNull($column)
                    ->orWhere($column, '');
            });
        } elseif ($filterBlank === 'not_blank') {
            $query->whereNotNull($column)
                ->where($column, '!=', '');
        }
    }

    protected function applyInvestmentBooleanFilter($query, $column, $filterValue, $filterBlank)
    {
        if ($this->isFilledFilterValue($filterValue) || $filterBlank === 'not_blank') {
            $query->whereHas('investment', function ($q) use ($column) {
                $q->whereNotNull($column);
            });
            return;
        }

        if ($filterBlank !== 'blank') {
            return;
        }

        $query->where(function ($q) use ($column) {
            $q->whereDoesntHave('investment')
                ->orWhereHas('investment', function ($iq) use ($column) {
                    $iq->whereNull($column);
                });
        });
    }

    protected function applyAnsinSupportFilter($query, $filterValue, $filterBlank)
    {
        if ($this->isFilledFilterValue($filterValue) || $filterBlank === 'not_blank') {
            $query->where(function ($q) {
                $q->whereHas('investmentRoomResident', function ($rq) {
                    $rq->whereNotNull('ansin_support');
                })->orWhereHas('investment', function ($iq) {
                    $iq->where('has_emergency_support', true);
                });
            });
            return;
        }

        if ($filterBlank !== 'blank') {
            return;
        }

        $query->where(function ($q) {
            $q->where(function ($qq) {
                $qq->whereDoesntHave('investmentRoomResident')
                    ->orWhereHas('investmentRoomResident', function ($rq) {
                        $rq->whereNull('ansin_support');
                    });
            })->where(function ($qq) {
                $qq->whereDoesntHave('investment')
                    ->orWhereHas('investment', function ($iq) {
                        $iq->where('has_emergency_support', false);
                    });
            });
        });
    }

    protected function applyTradingCompanyFilter($query, $filterValue, $filterBlank)
    {
        if ($filterValue !== '') {
            $query->where(function ($q) use ($filterValue) {
                if (is_numeric($filterValue)) {
                    $id = (int) $filterValue;
                    $q->orWhere('trading_company_1_id', $id)
                        ->orWhere('trading_company_2_id', $id)
                        ->orWhere('trading_company_3_id', $id);
                }

                $q->orWhereHas('tradingCompany1', function ($qq) use ($filterValue) {
                    $qq->where('name', 'like', '%' . $filterValue . '%');
                })->orWhereHas('tradingCompany2', function ($qq) use ($filterValue) {
                    $qq->where('name', 'like', '%' . $filterValue . '%');
                })->orWhereHas('tradingCompany3', function ($qq) use ($filterValue) {
                    $qq->where('name', 'like', '%' . $filterValue . '%');
                });
            });
            return;
        }

        if ($filterBlank === 'blank') {
            $query->whereNull('trading_company_1_id')
                ->whereNull('trading_company_2_id')
                ->whereNull('trading_company_3_id');
            return;
        }

        if ($filterBlank === 'not_blank') {
            $query->where(function ($q) {
                $q->whereNotNull('trading_company_1_id')
                    ->orWhereNotNull('trading_company_2_id')
                    ->orWhereNotNull('trading_company_3_id');
            });
        }
    }

    protected function isFilledFilterValue($filterValue): bool
    {
        if (is_array($filterValue)) {
            $from = trim((string) ($filterValue['from'] ?? ''));
            $to = trim((string) ($filterValue['to'] ?? ''));
            return $from !== '' || $to !== '';
        }

        return trim((string) $filterValue) !== '';
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
                    && !array_key_exists($field, $this->getBooleanRelationFilterMap())
                ) {
                    continue;
                }
                if (array_key_exists($field, $this->getBooleanRelationFilterMap())) {
                    $value = '';
                } else {
                    $value = $this->normalizeDateRangeValue($valueRaw);
                }
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
            ...$this->getLikeFilterFields(),
            'investment_name',
            'investment_room_number',
            'contractor_name',
            'category2_master',
            'category3_master',
            'genpuku_gyousha_id',
            ...array_keys($this->getBooleanRelationFilterMap()),
            ...$this->getDateRangeFilterFields(),
            ...array_keys($this->getDateRangeRelationFields()),
        ]);
    }

    protected function getSimpleFilterFields()
    {
        return [
            'id',
            'investment_id',
            'category1_master_id',
            'responsible_id',
            'executor_user_id',
            'next_action',
        ];
    }

    protected function getLikeFilterFields()
    {
        return [
            'procall_case_no',
            'title',
        ];
    }

    protected function getBooleanRelationFilterMap()
    {
        return [
            'facility_maintenance' => 'facility_maintenance',
            'three_repair' => 'three_repair',
            'ansin_support' => 'has_emergency_support',
        ];
    }

    protected function getDateRangeFilterFields()
    {
        return [
            'last_import_date',
            'nyuuden_date',
            'gencho_date',
            'cost_received_date',
            'own_suggestion_date',
            'own_consent_date',
            'pc_hachu_date',
            'kanko_yotei_date',
            'pc_kanko_receive_date',
            'pc_kanko_report_date',
            'kakumei_koujo_date',
            'complete_date',
        ];
    }

    protected function getDateRangeRelationFields()
    {
        return [];
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
