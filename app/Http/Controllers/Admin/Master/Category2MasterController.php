<?php
namespace App\Http\Controllers\Admin\Master;

use Illuminate\Http\Request;

class Category2MasterController
{
    public function index(Request $request)
    {
        return view('admin.master.category2-master.index');
    }
}
