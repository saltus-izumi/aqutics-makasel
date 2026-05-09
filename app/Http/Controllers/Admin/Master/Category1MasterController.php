<?php
namespace App\Http\Controllers\Admin\Master;

use Illuminate\Http\Request;

class Category1MasterController
{
    public function index(Request $request)
    {
        return view('admin.master.category1-master.index');
    }
}
