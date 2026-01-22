<?php

namespace App\Livewire\Owner\Operation;

use Livewire\Component;
use Illuminate\Support\Arr;

use App\Models\Thread;
use App\Models\OperationKind;
use App\Models\OperationTemplate;
use App\Models\Owner;
use App\Models\User;
use App\Models\Operation;
use App\Models\OperationFile;
use App\Models\ThreadMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OperationList extends Component
{
    public $ownerId = null;
    public $conditions = [];
    public $threads = [];
    public $selectedThreadId = null;
    public $selectedThread = null;
    public $selectedOperationId = null;
    public $modalTitle = '';
    public $modalFiles = [];
    public $previewTitle = '';
    public $previewUrl = '';
    public $previewType = '';
    public $body = '';

    protected $messages = [
        'body.required' => 'コメントを入力してください。',
    ];

    public function mount()
    {
    }

    public function render()
    {
        $this->refreshThreads();
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

            DB::transaction(function () use ($threadId) {
                ThreadMessage::where('thread_id', $threadId)
                    ->update(['read_at' => now()]);

                Operation::where('thread_id', $threadId)
                    ->update(['read_at' => now()]);
            });

        }
    }

    public function closeDetail()
    {
        $this->selectedThreadId = null;
        $this->selectedThread = null;
    }

    public function showCompletionPhotoFiles($operationId)
    {
        $operation = Operation::with('completionPhotoFiles.teProgressFile')->find($operationId);

        $this->modalTitle = '写真';
        $this->modalFiles = collect($operation?->completionPhotoFiles)
            ->map(function ($file) {
                return [
                    'id' => $file->id,
                    'name' => $file->teProgressFile?->file_name ?? $file->file_name ?? '',
                ];
            })
            ->filter(function ($file) {
                return !empty($file['name']);
            })
            ->values()
            ->all();

        $this->dispatch('open-files-modal');
    }

    public function showRetailEstimateFiles($operationId)
    {
        $operation = Operation::with('retailEstimateFiles.teProgressFile')->find($operationId);

        $this->modalTitle = 'お見積書';
        $this->modalFiles = collect($operation?->retailEstimateFiles)
            ->map(function ($file) {
                return [
                    'id' => $file->id,
                    'name' => $file->teProgressFile?->file_name ?? $file->file_name ?? '',
                ];
            })
            ->filter(function ($file) {
                return !empty($file['name']);
            })
            ->values()
            ->all();

        $this->dispatch('open-files-modal');
    }

    public function showOtherFiles($operationId)
    {
        $operation = Operation::with('otherFiles')->find($operationId);

        $this->modalTitle = 'その他のファイル';
        $this->modalFiles = collect($operation?->otherFiles)
            ->map(function ($file) {
                return [
                    'id' => $file->id,
                    'name' => $file->file_name ?? '',
                ];
            })
            ->filter(function ($file) {
                return !empty($file['name']);
            })
            ->values()
            ->all();

        $this->dispatch('open-files-modal');
    }

    public function showFilePreview($operationFileId)
    {
        $operationFile = OperationFile::with('teProgressFile')->find($operationFileId);
        if (!$operationFile) {
            return;
        }

        $fileName = $operationFile->teProgressFile?->file_name ?? $operationFile->file_name ?? '';
        $extension = Str::lower(pathinfo($fileName, PATHINFO_EXTENSION));
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];

        $this->previewTitle = $fileName ?: 'プレビュー';
        $this->previewUrl = route('owner.operation.files.preview', ['operationFileId' => $operationFileId]);
        if ($extension === 'pdf') {
            $this->previewType = 'pdf';
        } elseif (in_array($extension, $imageExtensions, true)) {
            $this->previewType = 'image';
        } else {
            $this->previewType = 'other';
        }

        $this->dispatch('open-preview-modal');
    }

    public function showAcceptModal($operationId)
    {
        $this->body = null;
        $this->selectedOperationId = $operationId;
        $this->dispatch('open-accept-modal');
    }

    public function showRejectModal($operationId)
    {
        $this->body = null;
        $this->selectedOperationId = $operationId;
        $this->dispatch('open-reject-modal');
    }

    public function showMessageModal()
    {
        $this->body = null;
        $this->dispatch('open-message-modal');
    }

    public function answer($status, $operationId)
    {
        $this->validate([
            'body' => [$status == Operation::STATUS_APPROVED ? '' : 'required'],
        ]);

        $operation = Operation::find($operationId);
        if (!$operation) {
            return;
        }

        DB::transaction(function () use ($operation, $status) {
            if ($this->body) {
                $threadMessage = ThreadMessage::create([
                    'thread_id' => $this->selectedThreadId,
                    'message_type' => ThreadMessage::MESSAGE_TYPE_OPERATION_REPLY,
                    'sender_type' => ThreadMessage::SENDER_TYPE_OWNER,
                    'body' => $this->body,
                    'status' => ThreadMessage::STATUS_SENT,
                    'sent_at' => now(),
                ]);
                $operation->owner_message_id = $threadMessage->id;
            }

            if ($this->selectedThread->last_operation?->id == $operation->id) {
                switch ($status) {
                    case Operation::STATUS_APPROVED:
                        $this->selectedThread->status = Thread::STATUS_OWNER_APPROVED;
                        break;
                    case Operation::STATUS_REJECTED:
                        $this->selectedThread->status = Thread::STATUS_OWNER_REJECTED;
                        break;
                }
                $this->selectedThread->save();
            }

            $operation->status = $status;
            $operation->save();
        });

        $this->selectedThread = Thread::with([
            'threadMessages' => fn ($q) => $q->orderBy('id', 'asc'),
        ])->find($this->selectedThreadId);

        $this->refreshThreads();

        $this->reset('body');
        $this->dispatch('close-accept-modal');
        $this->dispatch('close-reject-modal');
        $this->closeDetail();
    }

    public function message()
    {
        $this->validate([
            'body' => ['required'],
        ]);

        DB::transaction(function () {
            $threadMessage = ThreadMessage::create([
                'thread_id' => $this->selectedThreadId,
                'message_type' => ThreadMessage::MESSAGE_TYPE_CHAT_MESSAGE,
                'sender_type' => ThreadMessage::SENDER_TYPE_OWNER,
                'body' => $this->body,
                'status' => ThreadMessage::STATUS_SENT,
                'sent_at' => now(),
            ]);
        });

        $this->selectedThread = Thread::with([
            'threadMessages' => fn ($q) => $q->orderBy('id', 'asc'),
        ])->find($this->selectedThreadId);

        $this->refreshThreads();

        $this->reset('body');
        $this->dispatch('close-message-modal');
        $this->closeDetail();
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
            ->whereNot('status', Thread::STATUS_DRAFT)
            ->where('owner_id', $this->ownerId)
            ->orderBy('last_post_at', 'desc');

        if ($investmentId = ($this->conditions['investment_id'] ?? '')) {
            $query->where('investment_id', $investmentId);
        }

        if ($operationKindId = ($this->conditions['operation_kind_id'] ?? '')) {
            $query->whereHas('threadMessages.operation', function ($q) use ($operationKindId) {
                $q->where('operation_kind_id', $operationKindId);
            });
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
