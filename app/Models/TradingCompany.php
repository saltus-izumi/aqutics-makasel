<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TradingCompany extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    protected $table = 'trading_companies';

    protected $guarded = [
        'id'
    ];
}
