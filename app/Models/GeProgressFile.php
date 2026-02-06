<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use App\Models\Progress;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeProgressFile extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    PUBLIC CONST FILE_KIND_STEP1 = 1;

    protected $table = 'ge_progress_files';

    protected $guarded = [
        'id'
    ];
}
