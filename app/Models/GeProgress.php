<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use App\Models\Progress;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeProgress extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    protected $table = 'ge_progresses';

    protected $guarded = [
        'id'
    ];

    public function progress()
    {
        return $this->belongsTo(Progress::class);
    }
}
