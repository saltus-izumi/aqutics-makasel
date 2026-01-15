<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\Admin\Operation\StoreRequest;
use App\Models\Operation;
use App\Models\OperationFile;
use App\Models\OperationKind;
use App\Models\OperationTemplate;
use App\Models\Owner;
use App\Models\TeProgress;
use App\Models\Thread;
use App\Models\ThreadMessage;
use App\Models\User;

class OperationController
{
    public function index(Request $request)
    {
        $userOptions = User::getOptions();
        $ownerOptions = Owner::getOptions();
        $operationTemplateOptions = OperationTemplate::getGroupOptions();
        $operationKindOptions = OperationKind::getGroupOptions();
        $threadStatusOptions = Arr::except(Thread::STATUS, [Thread::STATUS_DRAFT]);
        $isReadOptions = [
            '1' => '既読',
            '2' => '未読',
        ];

        $conditions = $request->query();


        $query = Thread::with([
                'user',
                'owner',
                'operations',
                'operations.assignedUser',
                'operations.createdUser',
                'operations.operationKind',
                'operations.operationTemplate',
                'operations.investment',
                'operations.investmentRoom',
                'operations.threadMessage',
            ])
            ->where('thread_type', Thread::THREAD_TYPE_OPERATION)
            ->orderBy('last_post_at', 'desc');

        $threads = $query->get();

        return view('admin.operation.index')
            ->with(compact(
                'userOptions',
                'ownerOptions',
                'operationTemplateOptions',
                'operationKindOptions',
                'threadStatusOptions',
                'isReadOptions',
                'conditions',
                'threads',
            ));
    }

    public function create()
    {
        return view('admin.operation.create');
    }

    public function store(StoreRequest $request)
    {
        $teProgress = TeProgress::findOrNew($request->input('te_progress_id'));

        DB::transaction(function () use ($request, $teProgress) {
            $thread = Thread::findOrNew($request->input('thread_id'));
            if (!$thread->exists) {
                $thread->fill([
                    'thread_type' => Thread::THREAD_TYPE_OPERATION,
                    'user_id' => $teProgress->exists ? $teProgress->responsible_id : Auth::id(),
                    'owner_id' => $request->input('owner_id'),
                    'investment_id' => $request->input('investment_id'),
                    'investment_room_id' => $request->input('investment_room_id'),
                ]);
                // dump($thread);
            }
            $thread->first_post_at = !$request->input('is_draft') && !$thread->first_post_at  ? now() : null;
            $thread->last_post_at = !$request->input('is_draft') && !$thread->last_post_at  ? now() : null;
            $thread->save();

            $threadMessage = ThreadMessage::findOrNew($request->input('thread_message_id'));
            $threadMessage->fill([
                'thread_id' => $thread->id,
                'sender_type' => ThreadMessage::SENDER_TYPE_USER,
                'sender_user_id' => Auth::id(),
                'title' => $request->input('title'),
                'body' => $request->input('template'),
                'extended_message' => $request->input('message'),
                'status' => $request->input('is_draft') ? ThreadMessage::STATUS_DRAFT : ThreadMessage::STATUS_SENT,
            ]);
            $threadMessage->sent_at = !$request->input('is_draft') && !$threadMessage->sent_at  ? now() : null;
            $threadMessage->save();

            $operation = Operation::findOrNew($request->input('operation_id'));
            if (!$operation->exists) {
                $operation->fill([
                    'thread_id' => $thread->id,
                    'thread_message_id' => $threadMessage->id,
                    'operation_kind_id' => $request->input('operation_kind_id'),
                    'operation_template_id' => $request->input('operation_template_id'),
                    'assigned_user_id' => $teProgress->exists ? $teProgress->responsible_id : Auth::id(),
                    'created_user_id' => Auth::id(),
                    'owner_id' => $request->input('owner_id'),
                    'investment_id' => $request->input('investment_id'),
                    'investment_room_id' => $request->input('investment_room_id'),
                    'te_progress_id' => $teProgress->exists ? $teProgress->id : null,
                ]);
                // dump($operation);
            }
            $operation->status = $request->input('is_draft') ? Operation::STATUS_DRAFT : Operation::STATUS_IN_PROGRESS;
            $operation->sent_at = !$request->input('is_draft') && !$operation->sent_at  ? now() : null;

            $operation->save();

            $files = $request->file('operation_files'); // array of UploadedFile or null
            dump($files);
            if ($request->hasFile('operation_files')) {
                foreach ($files as $file) {
                    $original = $file->getClientOriginalName();
                    $path = $file->store("operations/{$operation->id}"); // 例
                    $operationFile = new OperationFile([
                        'operation_id' => $operation->id,
                        'file_kind' => OperationFile::FILE_KIND_OTHER,
                        'file_name' => $original,
                        'file_path' => $path,
                        'upload_at' => now(),
                    ]);
                    $operationFile->save();
                }
            }
        });

        return redirect()
            ->route('admin.operation.index')
            ->with('message', 'オペレーションを作成しました。');
    }


}
