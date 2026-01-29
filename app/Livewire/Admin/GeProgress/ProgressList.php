<?php

namespace App\Livewire\Admin\GeProgress;

use App\Models\Progress;
use Livewire\Component;

class ProgressList extends Component
{
    public $conditions = [];
    public $progresses = null;

    public function mount()
    {
        $this->refreshGeProgresses();
    }

    public function render()
    {
        return view('livewire.admin.ge-progress.progress-list');
    }

    protected function refreshGeProgresses() {
        $query = Progress::with([
            'investment',
            'investmentRoom',
            'investmentEmptyRoom',
        ]);

        $this->progresses = $query->get();
    }

    public function updateDate($progressId, $field, $date)
    {
        $progress = Progress::query()
            ->with([
                'investment',
                'investmentRoom',
                'investmentEmptyRoom',
            ])
            ->find($progressId);

        $progress->{$field} = $date;
        $progress->save();
    }
}
