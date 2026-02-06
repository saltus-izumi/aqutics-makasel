<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;

class InvestmentRoomResident extends Model
{
    use RecordsUserStamps;

    protected $guarded = [
        'id'
    ];

    /**
     * この入居者が属する物件を取得
     */
    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }

    /**
     * この入居者が属する物件を取得
     */
    public function investmentRoom()
    {
        return $this->belongsTo(InvestmentRoom::class);
    }
}
