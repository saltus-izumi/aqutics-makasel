<?php

namespace App\Livewire\Admin\Operation;

use Livewire\Component;
use Illuminate\Support\Arr;

use App\Models\Thread;
use App\Models\Operation;
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
        $this->refreshThreads();
    }

    public function render()
    {
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
                'threadMessages' => fn ($q) => $q->orderBy('id', 'desc'),
                'threadMessages.senderUser',
                'threadMessages.operation',
                'threadMessages.operation.ownerMessage',
                'owner',
                'investment',
                'investmentRoom',
                'firstOperationRelation',
                'firstOperationRelation.assignedUser',
                'firstOperationRelation.createdUser',
                'firstOperationRelation.owner',
                'firstOperationRelation.operationKind',
                'firstOperationRelation.operationTemplate',
                'firstOperationRelation.investment',
                'firstOperationRelation.investmentRoom',
                'firstOperationRelation.threadMessage',
                'lastOperationRelation',
                'lastOperationRelation.assignedUser',
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
                'firstOperationRelation',
                'firstOperationRelation.assignedUser',
                'firstOperationRelation.createdUser',
                'firstOperationRelation.owner',
                'firstOperationRelation.operationKind',
                'firstOperationRelation.operationTemplate',
                'firstOperationRelation.investment',
                'firstOperationRelation.investmentRoom',
                'firstOperationRelation.threadMessage',
                'lastOperationRelation',
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

        // ステータス
        if ($threadStatus = ($this->conditions['thread_status'] ?? '')) {
            $query->where('status', $threadStatus);
        }

        // 既読 / 未読
        if ($isRead = ($this->conditions['is_read'] ?? '')) {
            if ($isRead == 1) {
                $query->whereDoesntHave('threadMessages', function ($q) {
                    $q->whereNull('read_at');
                });
            } else {
                $query->whereHas('threadMessages', function ($q) {
                    $q->whereNull('read_at');
                });
            }
        }

        // 作成日
        if ($firstPostAtFrom = ($this->conditions['first_post_at_from'] ?? '')) {
            $query->where('first_post_at', '>=', $firstPostAtFrom . ' 00:00:00');
        }
        if ($firstPostAtTo = ($this->conditions['first_post_at_to'] ?? '')) {
            $query->where('first_post_at', '<=', $firstPostAtTo . ' 23:59:59');
        }

        // 下書き
        if ($isDraft = ($this->conditions['is_draft'] ?? '')) {
            $query->whereHas('threadMessages.operation', function ($q) use ($isDraft) {
                $q->where('status', Operation::STATUS_DRAFT);
            });
        }

        // 物件ID
        if ($investmentId = ($this->conditions['investment_id'] ?? '')) {
            $query->where('investment_id', $investmentId);
        }

        $this->threads = $query->get();
    }
}
