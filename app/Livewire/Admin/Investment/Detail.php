<?php

namespace App\Livewire\Admin\Investment;

use App\Models\CityRank;
use App\Models\InvestmentFloorPlan;
use App\Models\InvestmentNearestBusStop;
use App\Models\InvestmentNearestStation;
use Livewire\Component;
use Livewire\WithPagination;

class Detail extends Component
{
    use WithPagination;

    public array $cityRankOptions = [];
    public array $nearestStations = [];
    public array $nearestBusStops = [];
    public array $floorPlans = [];
    public $investmentId = null;

    public function mount($investmentId = null)
    {
        $this->investmentId = $investmentId;
        $this->cityRankOptions = CityRank::getOptions();
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
            return;
        }

        unset($this->floorPlans[$index]);
        $this->floorPlans = array_values($this->floorPlans);
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
            'has_service_room' => '',
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
                'has_service_room' => $floorPlan->has_service_room ?? '',
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
