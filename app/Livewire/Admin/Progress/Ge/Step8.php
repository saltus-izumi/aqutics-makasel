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

    public $progress = null;

    public string $componentId = '';

    protected $listeners = ['geProgressUpdated' => 'reloadProgress'];

    public function mount($progress)
    {
        $this->progress = $progress;
    }

    public function render()
    {
        return view('livewire.admin.progress.ge.step8');
    }
}
