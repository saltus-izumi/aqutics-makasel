<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\Admin\Operation\StoreRequest;
use App\Models\Operation;
use App\Models\OperationFile;
use App\Models\OperationKind;
use App\Models\OperationTemplate;
use App\Models\Owner;
use App\Models\TeProgress;
use App\Models\Thread;
use App\Models\ThreadMessage;
use App\Models\User;

class ImportController
{
    public function index(Request $request)
    {
    }

    public function procallAdd()
    {
        return view('admin.import.procall-add');
    }

}
