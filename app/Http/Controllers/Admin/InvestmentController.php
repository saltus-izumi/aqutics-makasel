<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\EquipmentCategory1Master;

class InvestmentController
{
    public function index(Request $request)
    {
        return view('admin.investment.index');
    }

    public function detail(Request $request, $investmentId)
    {
        return view('admin.investment.detail')
            ->with(compact(
                'investmentId'
            ));
    }
}
