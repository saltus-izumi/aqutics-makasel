<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\Admin\Operation\StoreRequest;
use App\Models\Operation;
use App\Models\TeProgress;
use App\Models\Thread;
use App\Models\ThreadMessage;

class OperationController
{
    public function index()
    {
        return view('admin.operation.index');
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
                    'owner_id' => $request->input('owner_id'),
                    'investment_id' => $request->input('investment_id'),
                    'investment_room_id' => $request->input('investment_room_id'),
                ]);
                dump($thread);
            }
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
                'sent_at' => !$request->input('is_draft') && !$threadMessage->sent_at  ? now() : null,
            ]);
            $threadMessage->save();

            $operation = Operation::findOrNew($request->input('operation_id'));
            if (!$operation->exists) {
                $operation->fill([
                    'thread_id' => $thread->id,
                    'thread_message_id' => $threadMessage->id,
                    'operation_template_id' => $request->input('operation_template_id'),
                    'assigned_user_id' => $teProgress->exists ? $teProgress->responsible_id : null,
                    'created_user_id' => Auth::id(),
                    'owner_id' => $request->input('owner_id'),
                    'investment_id' => $request->input('investment_id'),
                    'investment_room_id' => $request->input('investment_room_id'),
                    'te_progress_id' => $teProgress->exists ? $teProgress->id : null,
                ]);
                dump($operation);
            }
            $operation->status = $request->input('is_draft') ? Operation::STATUS_DRAFT : Operation::STATUS_IN_PROGRESS;
            $operation->sent_at = !$request->input('is_draft') && !$threadMessage->sent_at  ? now() : null;

            $operation->save();

            $files = $request->file('operation_files'); // array of UploadedFile or null
            dump($files);
            if ($request->hasFile('operation_files')) {
                foreach ($files as $file) {
                    // $file is Illuminate\Http\UploadedFile
                    $original = $file->getClientOriginalName();
                    $path = $file->store('operations'); // 例
                }
            }

            dd($request->input());

        });




    }


}
