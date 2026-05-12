<?php

namespace App\Livewire\Admin\Investment;

use App\Models\CityRank;
use App\Models\Investment;
use App\Models\InvestmentFloorPlan;
use App\Models\InvestmentNearestBusStop;
use App\Models\InvestmentNearestStation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Detail extends Component
{
    use WithPagination;

    public array $cityRankOptions = [];
    public array $nearestStations = [];
    public array $nearestBusStops = [];
    public array $floorPlans = [];
    public array $userOptions = [];
    public array $investmentValues = [];
    public ?string $savedMessage = null;
    public array $hasServiceRoomOptions = [
        'false' => '　',
        'true' => '+S',
    ];
    public array $managementPlanOptions = [
        1 => '標準プラン',
    ];
    public array $cleaningPlanOptions = [
        'スタンダード' => [
            1 => '月1回',
            2 => '月2回',
            3 => '月4回',
        ],
        'ワイド' => [
            4 => '月1回',
            5 => '月2回',
            6 => '月4回',
        ],
        'シンプル' => [
            7 => '月1回',
            8 => '月2回',
            9 => '月4回',
        ],
    ];
    public array $floorPlanOptions = [
        '1R' => '1R',
        '1K' => '1K',
        '1DK' => '1DK',
        '1LDK' => '1LDK',
        '2K' => '2K',
        '2DK' => '2DK',
        '2LDK' => '2LDK',
        '3LDK' => '3LDK',
        '4LDK' => '4LDK',
    ];
    public $investmentId = null;

    public function mount($investmentId = null)
    {
        $this->investmentId = $investmentId;
        $this->cityRankOptions = CityRank::getOptions();
        $this->userOptions = User::getOptions();
        $this->investmentValues = $this->getInvestmentValues();
        $this->nearestStations = $this->getNearestStations();
        $this->nearestBusStops = $this->getNearestBusStops();
        $this->floorPlans = $this->getFloorPlans();
    }

    public function addNearestStation(): void
    {
        $this->nearestStations[] = $this->emptyNearestStation();
    }

    public function removeNearestStation(int $index): void
    {
        if (count($this->nearestStations) <= 1) {
            $this->nearestStations = [
                $this->emptyNearestStation(),
            ];
            return;
        }

        unset($this->nearestStations[$index]);
        $this->nearestStations = array_values($this->nearestStations);
    }

    public function addNearestBusStop(): void
    {
        $this->nearestBusStops[] = $this->emptyNearestBusStop();
    }

    public function removeNearestBusStop(int $index): void
    {
        if (count($this->nearestBusStops) <= 1) {
            $this->nearestBusStops = [
                $this->emptyNearestBusStop(),
            ];
            return;
        }

        unset($this->nearestBusStops[$index]);
        $this->nearestBusStops = array_values($this->nearestBusStops);
    }

    public function addFloorPlan(): void
    {
        $this->floorPlans[] = $this->emptyFloorPlan();
    }

    public function removeFloorPlan(int $index): void
    {
        if (count($this->floorPlans) <= 1) {
            $this->floorPlans = [
                $this->emptyFloorPlan(),
            ];
            return;
        }

        unset($this->floorPlans[$index]);
        $this->floorPlans = array_values($this->floorPlans);
    }

    public function save(array $form): void
    {
        DB::transaction(function () use ($form) {
            $investment = $this->investmentId
                ? Investment::query()->findOrFail($this->investmentId)
                : new Investment();

            $investment->forceFill($this->investmentAttributes($form));
            $investment->save();

            $this->investmentId = $investment->id;

            $this->syncNearestStations($form['investment_nearest_stations'] ?? []);
            $this->syncNearestBusStops($form['investment_nearest_bus_stops'] ?? []);
            $this->syncFloorPlans($form['investment_floor_plans'] ?? []);
        });

        $this->investmentValues = $this->getInvestmentValues();
        $this->nearestStations = $this->getNearestStations();
        $this->nearestBusStops = $this->getNearestBusStops();
        $this->floorPlans = $this->getFloorPlans();
        $this->savedMessage = '保存しました。';
    }

    private function getInvestmentValues(): array
    {
        $keys = [
            'id',
            'management_agreement_url',
            'city_rank_id',
            'management_contract_date',
            'investment_name',
            'structure_floors',
            'address',
            'building_year',
            'kosu',
            'le_staff_id',
            'en_staff_id',
            'te_staff_id',
            'management_plan_id',
            'management_fee_rate',
            'recruitment_fee_rate',
            'renewal_fee_rate',
            'emergency_amount',
            'system_amount',
            'cleaning_plan_id',
            'cleaning_fee_amount',
            'garbage_option_amount',
            'building_maintenance_plan_id',
            'building_maintenance_fee_amount',
        ];

        $values = array_fill_keys($keys, '');
        if (!$this->investmentId) {
            return $values;
        }

        $investment = Investment::query()->find($this->investmentId);
        if (!$investment) {
            return $values;
        }

        foreach ($keys as $key) {
            $values[$key] = $investment->{$key} ?? '';
        }

        $values['management_contract_date'] = $this->formatDateForInput($values['management_contract_date']);
        $values['building_year'] = $this->formatDateForInput($values['building_year']);

        return $values;
    }

    private function investmentAttributes(array $form): array
    {
        $integerFields = [
            'city_rank_id',
            'kosu',
            'le_staff_id',
            'en_staff_id',
            'te_staff_id',
            'management_plan_id',
            'management_fee_rate',
            'recruitment_fee_rate',
            'renewal_fee_rate',
            'emergency_amount',
            'system_amount',
            'cleaning_plan_id',
            'cleaning_fee_amount',
            'garbage_option_amount',
            'building_maintenance_plan_id',
            'building_maintenance_fee_amount',
        ];
        $dateFields = [
            'management_contract_date',
            'building_year',
        ];
        $stringFields = [
            'management_agreement_url',
            'investment_name',
            'structure_floors',
            'address',
        ];

        $attributes = [];
        foreach ($integerFields as $field) {
            if (array_key_exists($field, $form)) {
                $attributes[$field] = $this->nullableInteger($form[$field]);
            }
        }
        foreach ($dateFields as $field) {
            if (array_key_exists($field, $form)) {
                $attributes[$field] = $this->nullableDate($form[$field]);
            }
        }
        foreach ($stringFields as $field) {
            if (array_key_exists($field, $form)) {
                $attributes[$field] = $this->nullableString($form[$field]);
            }
        }

        return $attributes;
    }

    private function syncNearestStations(array $rows): void
    {
        $keptIds = [];
        foreach (array_values($rows) as $row) {
            $id = $this->nullableInteger($row['id'] ?? null);
            $attributes = [
                'railway_name' => $this->nullableString($row['railway_name'] ?? null),
                'line_name' => $this->nullableString($row['line_name'] ?? null),
                'station_name' => $this->nullableString($row['station_name'] ?? null),
                'walking_minutes' => $this->nullableInteger($row['walking_minutes'] ?? null),
            ];

            if ($id && $this->allEmpty($attributes)) {
                InvestmentNearestStation::query()
                    ->where('investment_id', $this->investmentId)
                    ->whereKey($id)
                    ->delete();
                continue;
            }
            if (!$id && $this->allEmpty($attributes)) {
                continue;
            }

            $station = $id
                ? InvestmentNearestStation::query()->where('investment_id', $this->investmentId)->find($id)
                : new InvestmentNearestStation(['investment_id' => $this->investmentId]);

            if (!$station) {
                continue;
            }

            $station->fill($attributes);
            $station->investment_id = $this->investmentId;
            $station->save();
            $keptIds[] = $station->id;
        }

        $this->deleteMissingRows(InvestmentNearestStation::class, $keptIds);
    }

    private function syncNearestBusStops(array $rows): void
    {
        $keptIds = [];
        foreach (array_values($rows) as $row) {
            $id = $this->nullableInteger($row['id'] ?? null);
            $attributes = [
                'bus_stop_name' => $this->nullableString($row['bus_stop_name'] ?? null),
                'walking_minutes' => $this->nullableInteger($row['walking_minutes'] ?? null),
                'nearest_line_name' => $this->nullableString($row['nearest_line_name'] ?? null),
                'nearest_station_name' => $this->nullableString($row['nearest_station_name'] ?? null),
                'bus_minutes_to_station' => $this->nullableInteger($row['bus_minutes_to_station'] ?? null),
            ];

            if ($id && $this->allEmpty($attributes)) {
                InvestmentNearestBusStop::query()
                    ->where('investment_id', $this->investmentId)
                    ->whereKey($id)
                    ->delete();
                continue;
            }
            if (!$id && $this->allEmpty($attributes)) {
                continue;
            }

            $busStop = $id
                ? InvestmentNearestBusStop::query()->where('investment_id', $this->investmentId)->find($id)
                : new InvestmentNearestBusStop(['investment_id' => $this->investmentId]);

            if (!$busStop) {
                continue;
            }

            $busStop->fill($attributes);
            $busStop->investment_id = $this->investmentId;
            $busStop->save();
            $keptIds[] = $busStop->id;
        }

        $this->deleteMissingRows(InvestmentNearestBusStop::class, $keptIds);
    }

    private function syncFloorPlans(array $rows): void
    {
        $keptIds = [];
        foreach (array_values($rows) as $row) {
            $id = $this->nullableInteger($row['id'] ?? null);
            $attributes = [
                'floor_plan' => $this->nullableString($row['floor_plan'] ?? null),
                'has_service_room' => filter_var($row['has_service_room'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'area_sqm' => $this->nullableInteger($row['area_sqm'] ?? null),
            ];

            if ($id && $this->allEmpty([$attributes['floor_plan'], $attributes['area_sqm']])) {
                InvestmentFloorPlan::query()
                    ->where('investment_id', $this->investmentId)
                    ->whereKey($id)
                    ->delete();
                continue;
            }
            if (!$id && $this->allEmpty([$attributes['floor_plan'], $attributes['area_sqm']])) {
                continue;
            }

            $floorPlan = $id
                ? InvestmentFloorPlan::query()->where('investment_id', $this->investmentId)->find($id)
                : new InvestmentFloorPlan(['investment_id' => $this->investmentId]);

            if (!$floorPlan) {
                continue;
            }

            $floorPlan->fill($attributes);
            $floorPlan->investment_id = $this->investmentId;
            $floorPlan->save();
            $keptIds[] = $floorPlan->id;
        }

        $this->deleteMissingRows(InvestmentFloorPlan::class, $keptIds);
    }

    private function deleteMissingRows(string $modelClass, array $keptIds): void
    {
        $query = $modelClass::query()->where('investment_id', $this->investmentId);
        if ($keptIds) {
            $query->whereNotIn('id', $keptIds);
        }
        $query->delete();
    }

    private function nullableString($value): ?string
    {
        $value = trim((string) ($value ?? ''));

        return $value === '' ? null : $value;
    }

    private function nullableInteger($value): ?int
    {
        $value = str_replace(',', '', trim((string) ($value ?? '')));

        return $value === '' ? null : (int) $value;
    }

    private function nullableDate($value): ?string
    {
        $value = trim((string) ($value ?? ''));
        if ($value === '') {
            return null;
        }

        return str_replace('/', '-', $value);
    }

    private function formatDateForInput($value): string
    {
        if (!$value) {
            return '';
        }

        return str_replace('-', '/', (string) $value);
    }

    private function allEmpty(array $values): bool
    {
        foreach ($values as $value) {
            if ($value !== null && $value !== '') {
                return false;
            }
        }

        return true;
    }

    private function emptyNearestStation(): array
    {
        return [
            'id' => null,
            '_key' => uniqid('nearest_station_'),
            'railway_name' => '',
            'line_name' => '',
            'station_name' => '',
            'walking_minutes' => '',
        ];
    }

    private function getNearestStations(): array
    {
        if (!$this->investmentId) {
            return [
                $this->emptyNearestStation(),
            ];
        }

        $nearestStations = InvestmentNearestStation::query()
            ->where('investment_id', $this->investmentId)
            ->orderBy('id')
            ->get(['id', 'railway_name', 'line_name', 'station_name', 'walking_minutes'])
            ->map(fn (InvestmentNearestStation $nearestStation) => [
                'id' => $nearestStation->id,
                '_key' => 'nearest_station_' . $nearestStation->id,
                'railway_name' => $nearestStation->railway_name ?? '',
                'line_name' => $nearestStation->line_name ?? '',
                'station_name' => $nearestStation->station_name ?? '',
                'walking_minutes' => $nearestStation->walking_minutes ?? '',
            ])
            ->values()
            ->all();

        return $nearestStations ?: [
            $this->emptyNearestStation(),
        ];
    }

    private function emptyNearestBusStop(): array
    {
        return [
            'id' => null,
            '_key' => uniqid('nearest_bus_stop_'),
            'bus_stop_name' => '',
            'walking_minutes' => '',
            'nearest_line_name' => '',
            'nearest_station_name' => '',
            'bus_minutes_to_station' => '',
        ];
    }

    private function getNearestBusStops(): array
    {
        if (!$this->investmentId) {
            return [
                $this->emptyNearestBusStop(),
            ];
        }

        $nearestBusStops = InvestmentNearestBusStop::query()
            ->where('investment_id', $this->investmentId)
            ->orderBy('id')
            ->get(['id', 'bus_stop_name', 'walking_minutes', 'nearest_line_name', 'nearest_station_name', 'bus_minutes_to_station'])
            ->map(fn (InvestmentNearestBusStop $nearestBusStop) => [
                'id' => $nearestBusStop->id,
                '_key' => 'nearest_bus_stop_' . $nearestBusStop->id,
                'bus_stop_name' => $nearestBusStop->bus_stop_name ?? '',
                'walking_minutes' => $nearestBusStop->walking_minutes ?? '',
                'nearest_line_name' => $nearestBusStop->nearest_line_name ?? '',
                'nearest_station_name' => $nearestBusStop->nearest_station_name ?? '',
                'bus_minutes_to_station' => $nearestBusStop->bus_minutes_to_station ?? '',
            ])
            ->values()
            ->all();

        return $nearestBusStops ?: [
            $this->emptyNearestBusStop(),
        ];
    }

    private function emptyFloorPlan(): array
    {
        return [
            'id' => null,
            '_key' => uniqid('floor_plan_'),
            'floor_plan' => '',
            'has_service_room' => 'false',
            'area_sqm' => '',
        ];
    }

    private function getFloorPlans(): array
    {
        if (!$this->investmentId) {
            return [
                $this->emptyFloorPlan(),
            ];
        }

        $floorPlans = InvestmentFloorPlan::query()
            ->where('investment_id', $this->investmentId)
            ->orderBy('id')
            ->get(['id', 'floor_plan', 'has_service_room', 'area_sqm'])
            ->map(fn (InvestmentFloorPlan $floorPlan) => [
                'id' => $floorPlan->id,
                '_key' => 'floor_plan_' . $floorPlan->id,
                'floor_plan' => $floorPlan->floor_plan ?? '',
                'has_service_room' => $floorPlan->has_service_room ? 'true' : 'false',
                'area_sqm' => $floorPlan->area_sqm ?? '',
            ])
            ->values()
            ->all();

        return $floorPlans ?: [
            $this->emptyFloorPlan(),
        ];
    }
    
    public function render()
    {
        return view('livewire.admin.investment.detail');
    }
}
