<?php
namespace App\Http\Controllers\Owner;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\Admin\Operation\StoreRequest;
use App\Models\Investment;
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
        $operationKindOptions = OperationKind::getGroupOptions();
        $threadStatusOptions = Arr::except(Thread::OWNER_STATUS, [Thread::STATUS_DRAFT, Thread::STATUS_REPROPOSED]);

        $conditions = $request->query();

        $investment = null;
        if ($investmentId = ($conditions['investment_id'] ?? '')) {
            $investment = Investment::find($investmentId);
        }

        return view('owner.operation.index')
            ->with(compact(
                'investment',
                'operationKindOptions',
                'threadStatusOptions',
                'conditions',
            ));
    }

    public function create($operationId = null, $teProgressId = null, $geProgressId = null)
    {
        return view('admin.operation.create')
            ->with(compact(
                'operationId',
                'teProgressId',
                'geProgressId',
            ));
    }

    public function previewFile($operationFileId)
    {
        $owner = Auth::guard('owner')->user();
        $operationFile = OperationFile::with('teProgressFile')->findOrFail($operationFileId);
        $operation = Operation::find($operationFile->operation_id);
        if (!$operation) {
            abort(404);
        }

        $thread = Thread::find($operation->thread_id);
        if (!$thread || $thread->owner_id !== $owner->id) {
            abort(403);
        }

        $filePath = $operationFile->teProgressFile?->file_path ?? $operationFile->file_path;
        $fileName = $operationFile->teProgressFile?->file_name ?? $operationFile->file_name ?? 'file';
        if (!$filePath || !Storage::disk('local')->exists($filePath)) {
            abort(404);
        }

        return response()->file(Storage::disk('local')->path($filePath), [
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
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
                    'status' => $request->input('is_draft') ? Thread::STATUS_DRAFT : Thread::STATUS_PROPOSED,
                ]);
                // dump($thread);
            }
            $thread->status = !$request->input('is_draft') && $thread->status == Thread::STATUS_DRAFT ? Thread::STATUS_PROPOSED : $thread->status;
            $thread->first_post_at = !$request->input('is_draft') && !$thread->first_post_at  ? now() : null;
            $thread->last_post_at = !$request->input('is_draft') && !$thread->last_post_at  ? now() : null;
            $thread->save();

            $threadMessage = ThreadMessage::findOrNew($request->input('thread_message_id'));
            $threadMessage->fill([
                'thread_id' => $thread->id,
                'sender_type' => ThreadMessage::SENDER_TYPE_USER,
                'sender_user_id' => $threadMessage->sender_user_id ?? Auth::id(),
                'title' => $request->input('title'),
                'body' => $request->input('template'),
                'extended_message' => $request->input('message'),
                'status' => $request->input('is_draft') ? ThreadMessage::STATUS_DRAFT : ThreadMessage::STATUS_SENT,
            ]);
            $threadMessage->sent_at = !$request->input('is_draft') && !$threadMessage->sent_at  ? now() : null;
            $threadMessage->save();

            // $operation = Operation::findOrNew($request->input('operation_id'));
            $operation = Operation::with([
                    'retailEstimateFiles',
                    'completionPhotoFiles',
                    'otherFiles',
                ])
                ->find($request->input('operation_id')) ?? new Operation();
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
            } else {
                $operation->operation_kind_id = $request->input('operation_kind_id');
                $operation->operation_template_id = $request->input('operation_template_id');
            }
            $operation->status = $request->input('is_draft') ? Operation::STATUS_DRAFT : Operation::STATUS_IN_PROGRESS;
            $operation->sent_at = !$request->input('is_draft') && !$operation->sent_at  ? now() : null;
            $operation->save();

            // その他のファイル削除
            if ($request->input('operation_files_delete')) {
                foreach($request->input('operation_files_delete') as $deleteFileId) {
                    foreach ($operation->otherFiles as $otherFile) {
                        if ($otherFile->id == $deleteFileId) {
                            $otherFile->delete();
                            break;
                        }
                    }
                }
            }

            // その他のファイル
            $files = $request->file('operation_files'); // array of UploadedFile or null
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

            // 上代見積もり削除
            if ($operation->retailEstimateFiles) {
                foreach($operation->retailEstimateFiles as $retailEstimateFile) {
                    if (!in_array($retailEstimateFile->id, $request->input('retail_estimate_files') ?? [])) {
                        $retailEstimateFile->delete();
                    }
                }
            }

            // 上代見積もり
            if ($request->input('retail_estimate_files')) {
                foreach($request->input('retail_estimate_files') as $retailEstimateFileId) {
                    OperationFile::create([
                        'operation_id' => $operation->id,
                        'file_kind' => OperationFile::FILE_KIND_RETAIL_ESTIMATE,
                        'te_progress_file_id' => $retailEstimateFileId
                    ]);
                }
            }

            // 完工写真削除
            if ($operation->completionPhotoFiles) {
                foreach($operation->completionPhotoFiles as $completionPhotoFile) {
                    if (!in_array($completionPhotoFile->id, $request->input('completion_photo_files') ?? [])) {
                        $completionPhotoFile->delete();
                    }
                }
            }

            // 完工写真
            if ($request->input('completion_photo_files')) {
                foreach($request->input('completion_photo_files') as $completionPhotoFileId) {
                    OperationFile::create([
                        'operation_id' => $operation->id,
                        'file_kind' => OperationFile::FILE_KIND_COMPLETION_PHOTO,
                        'te_progress_file_id' => $completionPhotoFileId
                    ]);
                }
            }

        });

        return redirect()
            ->route('admin.operation.index')
            ->with('message', 'オペレーションを作成しました。');
    }


}
