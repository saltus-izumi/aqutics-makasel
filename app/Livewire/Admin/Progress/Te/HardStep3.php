<?php

namespace App\Livewire\Admin\Progress\Te;

use App\Models\TeProgress;
use Livewire\Component;
use Livewire\WithFileUploads;

class HardStep3 extends Component
{
    public $teProgress = null;

    protected $listeners = ['teProgressUpdated' => 'reloadProgress'];

    public function mount($teProgress)
    {
        $this->teProgress = $teProgress;
    }

    public function reloadProgress($teProgressId = null)
    {
        if (!$this->teProgress) {
            return;
        }

        if ($teProgressId !== null && (int) $teProgressId !== (int) $this->teProgress->id) {
            return;
        }

        $this->teProgress = TeProgress::query()
            ->find($this->teProgress->id);
    }

    public function render()
    {
        return view('livewire.admin.progress.te.hard-step3');
    }
}
