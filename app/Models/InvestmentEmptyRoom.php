<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;

class InvestmentEmptyRoom extends Model
{
    use RecordsUserStamps;

    /**
     * この部屋が属する物件を取得
     */
    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }
}
