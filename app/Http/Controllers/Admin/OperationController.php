<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationController
{
    public function index()
    {
        return view('admin.operation.index');
    }

}
