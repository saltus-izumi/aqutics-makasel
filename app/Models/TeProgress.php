<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeProgress extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    protected $table = 'te_progresses';

    protected $guarded = [
        'id'
    ];

}
