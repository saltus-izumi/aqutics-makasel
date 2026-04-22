<?php
namespace App\Http\Controllers\Admin\Master;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\Owner;

class MailTemplateController
{
    public function index(Request $request)
    {
        return view('admin.master.mail-template.index');
    }
}
