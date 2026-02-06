<?php

namespace App\Livewire\Admin\Progress\Ge;

use Livewire\Component;

class DetailHeader extends Component
{
    public $progress = null;
    public $lts = [

    ];

    public function mount()
    {

    }

    public function render()
    {
        return view('livewire.admin.progress.ge.detail-header');
    }
}
