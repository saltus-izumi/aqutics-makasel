<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquipmentCategory1Master extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    protected $guarded = [
        'id'
    ];

}
