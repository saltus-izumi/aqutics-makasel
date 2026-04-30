<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TradingCompanyArea extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    protected $table = 'trading_company_areas';

    protected $guarded = [
        'id',
    ];

    public function tradingCompany()
    {
        return $this->belongsTo(TradingCompany::class, 'trading_company_id', 'id');
    }

    public function investments()
    {
        return $this->hasMany(Investment::class, 'address_area_id', 'address_area_id');
    }
}
