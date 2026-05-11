<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvestmentFloorPlan extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'has_service_room' => 'boolean',
    ];

    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }
}
