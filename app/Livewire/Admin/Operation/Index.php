<?php

namespace App\Livewire\Admin\Operation;

use Livewire\Component;
use Illuminate\Support\Arr;

use App\Models\Thread;
use App\Models\OperationKind;
use App\Models\OperationTemplate;
use App\Models\Owner;
use App\Models\User;

class Index extends Component
{
    public $userOptions = [];
    public $ownerOptions = [];
    public $investmentOptions = [];
    public $investmentRoomOptions = [];
    public $operationTemplateOptions = [];
    public $operationKindOptions = [];
    public $threadStatusOptions = [];
    public $isReadOptions = [];

    public $assignedUserId = '';
    public $createdUserId = '';
    public $ownerId = '';
    public $operationTemplateId = '';
    public $operationKindId = '';
    public $threadStatus = '';
    public $isRead = '';
    public $firstPostAtFrom = '';
    public $firstPostAtTo = '';

    public function mount()
    {
        $this->userOptions = User::getOptions();
        $this->ownerOptions = Owner::getOptions();
        $this->operationTemplateOptions = OperationTemplate::getGroupOptions();
        $this->operationKindOptions = OperationKind::getGroupOptions();
        $this->threadStatusOptions = Arr::except(Thread::STATUS, [Thread::STATUS_DRAFT]);
        $this->isReadOptions = [
            '1' => '既読',
            '2' => '未読',
        ];
    }

    public function render()
    {
        return view('livewire.admin.operation.index');
    }
}
