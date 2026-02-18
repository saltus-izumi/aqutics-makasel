<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TradingCompany extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    public const KIND_REPAIR = 1;
    public const KIND_RESTORATION = 2;
    public const KIND_CLEANING = 3;
    public const KIND_MAINTENANCE = 4;

    public const KIND_SHORT = [
        self::KIND_REPAIR => '修会',
        self::KIND_RESTORATION => '原会',
        self::KIND_CLEANING => '清会',
        self::KIND_MAINTENANCE => '保守',
    ];

    public const TRADING_STATUS_ENABLE = 1;
    public const TRADING_STATUS_DISABLE = 2;
    public const TRADING_STATUS = [
        self::TRADING_STATUS_ENABLE => '現在取引中',
        self::TRADING_STATUS_DISABLE => '取引停止中',
    ];

    protected $table = 'trading_companies';

    protected $guarded = [
        'id'
    ];
}
