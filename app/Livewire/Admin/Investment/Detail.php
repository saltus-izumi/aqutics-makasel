<?php

namespace App\Livewire\Admin\Investment;

use App\Models\CityRank;
use App\Models\Investment;
use Livewire\Component;
use Livewire\WithPagination;

class Detail extends Component
{
    use WithPagination;

    public array $cityRankOptions = [];

    public function mount()
    {
        $this->cityRankOptions = CityRank::getOptions();
    }
    
    public function render()
    {
        return view('livewire.admin.investment.detail');
    }
}
