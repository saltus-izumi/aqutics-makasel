<?php
namespace App\Http\Controllers\Admin\Master;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\EquipmentCategory1Master;

class EquipmentCategory2MasterController
{
    public function index(Request $request)
    {
        return view('admin.master.equipment-category2-master.index');
    }
}
