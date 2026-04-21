<?php
namespace App\Http\Controllers\Admin\Progress;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\TeProgress;
use App\Models\TeProgressFile;

class TeController
{
    public function index(Request $request)
    {
        return view('admin.progress.te.index');
    }

    public function detail(Request $request, $teProgressId)
    {
        $teProgress = TeProgress::query()
            ->with([
            ])
            ->find($teProgressId);

        if (!$teProgress) {
            abort(404);
        }

        return view('admin.progress.te.detail')
            ->with(compact(
                'teProgress',
            ));
    }

    public function preview(Request $request, $teProgressFileId)
    {
        $file = TeProgressFile::query()->find($teProgressFileId);
        if (!$file) {
            abort(404);
        }

        $filePath = $file->file_path;
        $fileName = $file->file_name ?? 'file';
        if (!$filePath || !Storage::disk('local')->exists($filePath)) {
            abort(404);
        }

        return response()->file(Storage::disk('local')->path($filePath), [
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }

}
