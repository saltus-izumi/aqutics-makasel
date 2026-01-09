<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\Admin\Operation\StoreRequest;

class OperationController
{
    public function index()
    {
        return view('admin.operation.index');
    }

    public function create()
    {
        return view('admin.operation.create');
    }

    public function store(StoreRequest $request)
    {
        dump($request->input());


    }

}
