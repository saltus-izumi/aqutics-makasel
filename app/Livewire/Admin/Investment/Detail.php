<?php

namespace App\Livewire\Admin\Investment;

use App\Models\Investment;
use Livewire\Component;
use Livewire\WithPagination;

class Detail extends Component
{
    use WithPagination;

    public function mount()
    {
    }
    
    public function render()
    {
        return view('livewire.admin.investment.detail');
    }
}
