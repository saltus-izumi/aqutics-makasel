<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ksf extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    protected $guarded = [
        'id'
    ];

    public function kpyType()
    {
        return $this->belongsTo(KpiType::class);
    }

}
