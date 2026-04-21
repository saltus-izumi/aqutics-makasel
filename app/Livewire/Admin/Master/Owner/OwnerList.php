<?php

namespace App\Livewire\Admin\Master\Owner;

use App\Models\Owner;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class OwnerList extends Component
{
    public $owners = null;

    public function mount()
    {
        $this->owners = Owner::query()
            ->with([
                'landlords'
            ])
            ->orderBy('id', 'asc')
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.master.owner.owner-list');
    }
}
