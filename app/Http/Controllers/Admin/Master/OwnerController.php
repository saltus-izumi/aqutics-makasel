<?php
namespace App\Http\Controllers\Admin\Master;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\Owner;

class OwnerController
{
    public function index(Request $request)
    {
        return view('admin.master.owner.index');
    }

    public function detail(Request $request, $teProgressId)
    {
        $teProgress = Owner::query()
            ->with([
            ])
            ->find($teProgressId);

        if (!$teProgress) {
            abort(404);
        }

        return view('admin.master.te.detail')
            ->with(compact(
                'teProgress',
            ));
    }
}
