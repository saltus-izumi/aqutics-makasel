<?php

namespace App\Livewire\Admin\GeProgress;

use App\Models\Progress;
use Livewire\Attributes\On;
use Livewire\Component;

class ProgressList extends Component
{
    public $conditions = [];
    public $progresses = null;
    public bool $incompleteOnly = true;
    public string $searchKeyword = '';
    public string $sortOrder = 'asc';
    public string $filterId = '';

    public function mount()
    {
        $this->incompleteOnly = true;
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
                'investment.landlord.owner',
                'investmentRoom',
                'investmentEmptyRoom',
            ])
            ->whereNull('kaiyaku_cancellation_date')
            ->orderBy('id', $this->normalizeSortOrder($this->sortOrder));

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

        if ($date == 'ー') {
            $progress->{$field} = null;
            $progress->{$field . '_state'} = 2;
        }
        elseif ($date == '') {
            $progress->{$field} = $date;
            $progress->{$field . '_state'} = 0;
        }
        else {
            $progress->{$field} = $date;
            $progress->{$field . '_state'} = 1;
        }
        $progress->save();
        $this->refreshGeProgresses();
    }

    #[On('ge-progress:incomplete-only-changed')]
    public function updateIncompleteOnly($incompleteOnly)
    {
        $this->incompleteOnly = (bool) $incompleteOnly;
        $this->refreshGeProgresses();
    }

    #[On('ge-progress:search-input-submitted')]
    public function updateSearchKeyword($keyword)
    {
        $this->searchKeyword = trim((string) $keyword);
        $this->refreshGeProgresses();
    }

    public function updateSortFilter($sortOrder, $filterId)
    {
        $this->sortOrder = $this->normalizeSortOrder($sortOrder);
        $this->filterId = trim((string) $filterId);
        $this->refreshGeProgresses();
    }

    protected function setCondition($query)
    {
        if ($this->incompleteOnly) {
            $query->whereNull('ge_complete_date');
        }

        if ($this->searchKeyword !== '') {
            $keyword = $this->searchKeyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('investment_id', $keyword)
                    ->orWhereHas('investment', function ($q) use ($keyword) {
                        $q->where('investment_name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhereHas('investment.landlord.owner', function ($q) use ($keyword) {
                        $q->where('name', 'like', '%' . $keyword . '%')
                            ->orWhere('id', $keyword);
                    });
            });
        }

        if ($this->filterId !== '') {
            $query->where('id', 'like', '%' . $this->filterId . '%');
        }

        return $query;
    }

    protected function normalizeSortOrder($sortOrder)
    {
        return $sortOrder === 'desc' ? 'desc' : 'asc';
    }
}
