<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\EnProgress;
use App\Models\GeProgressFile;

class EnProgressController
{
    public function index(Request $request)
    {
        return view('admin.progress.en.index');
    }

    public function detail(Request $request, $enProgressId)
    {
        $enProgress = EnProgress::query()
            ->with([
                'responsibleUser',
                'executorUser',
                'broker',
                'progress',
                'progress.latestGeProgress',
                'progress.investment',
                'progress.investment.restorationCompany',
                'progress.investment.landlord.owner',
                'progress.investmentRoom',
                'progress.investmentRoomRedidentHistory',
                'progress.investmentEmptyRoom',
            ])
            ->find($enProgressId);

        if (!$enProgress) {
            abort(404);
        }

        return view('admin.progress.en.detail')
            ->with(compact(
                'enProgress',
            ));
    }

    public function ownerSettlement(Request $request, $geProgressId)
    {
        $geProgress = GeProgress::query()
            ->with([
                'step1Files',
                'responsibleUser',
                'executorUser',
                'progress',
                'progress.investment',
                'progress.investment.restorationCompany',
                'progress.investment.landlord.owner',
                'progress.investmentRoom',
                'progress.investmentRoomRedidentHistory',
                'progress.investmentEmptyRoom',
            ])
            ->find($geProgressId);

        if (!$geProgress) {
            abort(404);
        }

        return view('admin.progress.en.owner-settlement')
            ->with(compact(
                'geProgress',
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
