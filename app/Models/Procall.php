<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Procall extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    protected $table = 'procalls';

    protected $guarded = [
        'id'
    ];

}
