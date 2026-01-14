<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Landlord extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    /**
     * この家主が属するオーナーを取得
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    /**
     * この家主が持つ物件（Investment）を取得
     */
    public function investments()
    {
        return $this->hasMany(Investment::class);
    }
}
