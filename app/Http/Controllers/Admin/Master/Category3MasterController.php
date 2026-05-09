<?php
namespace App\Http\Controllers\Admin\Master;

use Illuminate\Http\Request;

class Category3MasterController
{
    public function index(Request $request)
    {
        return view('admin.master.category3-master.index');
    }
}
