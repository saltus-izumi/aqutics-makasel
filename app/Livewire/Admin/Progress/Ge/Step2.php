<?php

namespace App\Livewire\Admin\Progress\Ge;

use App\Models\Progress;
use Livewire\Component;

class Step2 extends Component
{
    public $progress = null;
    protected $listeners = ['geProgressUpdated' => 'reloadProgress'];

    public function reloadProgress($progressId = null)
    {
        if (!$this->progress) {
            return;
        }

        if ($progressId !== null && (int) $progressId !== (int) $this->progress->id) {
            return;
        }

        $this->progress = Progress::query()
            ->with('geProgress')
            ->find($this->progress->id);
    }

    public function render()
    {
        return view('livewire.admin.progress.ge.step2');
    }
}
