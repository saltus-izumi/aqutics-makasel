<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                'GeProgress',
                'GeProgress.executorUser',
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

}
