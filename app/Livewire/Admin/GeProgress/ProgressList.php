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
        $this->conditions += [
            'cmp' => '1',
        ];
        $this->refreshGeProgresses();
    }

    public function render()
    {
        return view('livewire.admin.ge-progress.progress-list');
    }

    protected function refreshGeProgresses() {
        $query = Progress::query()
            ->with([
                'investment',
                'investmentRoom',
                'investmentEmptyRoom',
            ])
            ->whereNull('kaiyaku_cancellation_date')
            ->orderBy('id', 'asc');

        $query = $this->setCondition($query);

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

        if (!$progress) {
            return;
        }

        $progress->{$field} = $date;
        $progress->save();
        $this->refreshGeProgresses();
    }

    protected function setCondition($query)
    {
        if (($this->conditions['cmp'] ?? '') == '1') {
            $query->whereNull('ge_complete_date');
        }

        return $query;
    }
}
