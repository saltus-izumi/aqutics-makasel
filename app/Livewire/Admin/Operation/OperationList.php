<?php

namespace App\Livewire\Admin\Operation;

use Livewire\Component;
use Illuminate\Support\Arr;

use App\Models\Thread;
use App\Models\OperationKind;
use App\Models\OperationTemplate;
use App\Models\Owner;
use App\Models\User;

class OperationList extends Component
{
    public $conditions = [];
    public $threads = [];
    public $selectedThreadId = null;
    public $selectedThread = null;

    public function mount()
    {
    }

    public function render()
    {
        $this->refreshThreads();
        return view('livewire.admin.operation.operation-list');
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

    protected function refreshThreads() {
        $query = Thread::with([
                'user',
                'owner',
                'threadMessages',
                'threadMessages.operation',
                'threadMessages.operation.assignedUser',
                'threadMessages.operation.createdUser',
                'threadMessages.operation.operationKind',
                'threadMessages.operation.operationTemplate',
                'threadMessages.operation.investment',
                'threadMessages.operation.investmentRoom',
                'threadMessages.operation.retailEstimateFiles',
                'threadMessages.operation.retailEstimateFiles.teProgressFile',
                'threadMessages.operation.completionPhotoFiles',
                'threadMessages.operation.completionPhotoFiles.teProgressFile',
                'threadMessages.operation.otherFiles',
            ])
            ->where('thread_type', Thread::THREAD_TYPE_OPERATION)
            ->orderBy('last_post_at', 'desc');

        // オペレーション所有者
        if ($assignedUserId = ($this->conditions['assigned_user_id'] ?? '')) {
            $query->whereHas('threadMessages.operation', function ($q) use ($assignedUserId) {
                $q->where('assigned_user_id', $assignedUserId);
            });
        }

        // 作成者
        if ($createdUserId = ($this->conditions['created_user_id'] ?? '')) {
            $query->whereHas('threadMessages.operation', function ($q) use ($createdUserId) {
                $q->where('created_user_id', $createdUserId);
            });
        }

        // オーナー
        if ($ownerId = ($this->conditions['owner_id'] ?? '')) {
            $query->whereHas('threadMessages.operation', function ($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            });
        }

        // カテゴリ
        if ($operationTemplateId = ($this->conditions['operation_template_id'] ?? '')) {
            $query->whereHas('threadMessages.operation', function ($q) use ($operationTemplateId) {
                $q->where('operation_template_id', $operationTemplateId);
            });
        }

        // オペレーション
        if ($operationKindId = ($this->conditions['operation_kind_id'] ?? '')) {
            $query->whereHas('threadMessages.operation', function ($q) use ($operationKindId) {
                $q->where('operation_kind_id', $operationKindId);
            });
        }

        if ($investmentId = ($this->conditions['investment_id'] ?? '')) {
            $query->where('investment_id', $investmentId);
        }


        if ($threadStatus = ($this->conditions['thread_status'] ?? '')) {
            if ($threadStatus == Thread::STATUS_PROPOSED) {
                $query->where(function ($q) {
                    $q->where('status', Thread::STATUS_PROPOSED)
                        ->orWhere('status', Thread::STATUS_REPROPOSED);
                });
            }
            else {
                $query->where('status', $threadStatus);
            }
        }

        $this->threads = $query->get();
    }
}
