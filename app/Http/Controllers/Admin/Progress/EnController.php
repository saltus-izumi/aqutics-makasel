<?php
namespace App\Http\Controllers\Admin\Progress;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\EnProgress;
use App\Models\GeProgressFile;

class EnController
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
                'enProgressIndividualApplicant',
                'enProgressCorporateApplicant',
                'enProgressOccupants',
                'enProgressEmergencyContact',
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

    public function approval(Request $request, $enProgressId)
    {
        $enProgress = EnProgress::query()
            ->with([
                'responsibleUser',
                'executorUser',
                'broker',
                'enProgressIndividualApplicant',
                'enProgressCorporateApplicant',
                'enProgressOccupants',
                'enProgressEmergencyContact',
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

        return view('admin.progress.en.approval')
            ->with(compact(
                'enProgress',
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
