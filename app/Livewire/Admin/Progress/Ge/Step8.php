<?php

namespace App\Livewire\Admin\Progress\Ge;

use App\Models\GeProgressFile;
use App\Models\Progress;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Step8 extends Component
{
    use WithFileUploads;

    public $geProgress = null;

    public string $componentId = '';

    protected $listeners = ['geProgressUpdated' => 'reloadProgress'];

    public function mount($geProgress)
    {
        $this->geProgress = $geProgress;
    }

    public function render()
    {
        return view('livewire.admin.progress.ge.step8');
    }
}
