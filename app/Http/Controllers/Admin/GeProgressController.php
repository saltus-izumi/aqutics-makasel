<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\GeProgressFile;
use App\Models\Progress;

class GeProgressController
{
    public function index(Request $request)
    {
        return view('admin.progress.ge.index');
    }

    public function detail(Request $request, $progressId)
    {
        $progress = Progress::query()
            ->with([
                'geProgress',
                'geProgress.step1Files',
                'geProgress.executorUser',
                'genpukuResponsible',
                'investment',
                'investment.restorationCompany',
                'investment.landlord.owner',
                'investmentRoom',
                'investmentRoomRedidentHistory',
                'investmentEmptyRoom',
            ])
            ->find($progressId);

        if (!$progress) {
            abort(404);
        }

        return view('admin.progress.ge.detail')
            ->with(compact(
                'progress',
            ));
    }

    public function preview(Request $request, $geProgressFileId)
    {
        $file = GeProgressFile::query()->find($geProgressFileId);
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
