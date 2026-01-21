<?php

namespace App\Livewire\Owner\Operation;

use Livewire\Component;
use Illuminate\Support\Arr;

use App\Models\Thread;
use App\Models\OperationKind;
use App\Models\OperationTemplate;
use App\Models\Owner;
use App\Models\User;

class OperationList extends Component
{
    public $threads = [];
    public $selectedThreadId = null;
    public $selectedThread = null;

    public function mount()
    {
    }

    public function render()
    {
        return view('livewire.owner.operation.operation-list');
    }

    public function selectThread($threadId)
    {
        if ($this->selectedThreadId == $threadId) {
            $this->selectedThreadId = null;
            $this->selectedThread = null;
        } else {
            $this->selectedThreadId = $threadId;
            $this->selectedThread = Thread::with([
                'threadMessages' => fn ($q) => $q->orderBy('id', 'asc'),
                // 'operations',
                // 'operations.investmentRoom',
                // 'operations.owner',
                // 'operations.operationTemplate',
                // 'operations.operationKind',
                // 'operations.assignedUser',
                // 'operations.createdUser',
            ])->find($threadId);
        }
    }

    public function closeDetail()
    {
        $this->selectedThreadId = null;
        $this->selectedThread = null;
    }
}
