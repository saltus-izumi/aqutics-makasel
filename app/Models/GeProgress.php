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

    PUBLIC CONST NEXT_ACTION_TAIKYO_UKETUKE = 1;
    PUBLIC CONST NEXT_ACTION_CANCELLATION = 2;
    PUBLIC CONST NEXT_ACTION_TAIKYO = 3;
    PUBLIC CONST NEXT_ACTION_GENPUKU_MITSUMORI_RECIEVED = 4;
    PUBLIC CONST NEXT_ACTION_TSUDEN = 5;
    PUBLIC CONST NEXT_ACTION_TENANT_CHARGE_CONFIRMED = 6;
    PUBLIC CONST NEXT_ACTION_GENPUKU_TEIAN = 7;
    PUBLIC CONST NEXT_ACTION_GENPUKU_TEIAN_KYODAKU = 8;
    PUBLIC CONST NEXT_ACTION_GENPUKU_KOUJI_HACHU = 9;
    PUBLIC CONST NEXT_ACTION_KANKO_YOTEI = 10;
    PUBLIC CONST NEXT_ACTION_KANKO_JYUSHIN = 11;
    PUBLIC CONST NEXT_ACTION_OWNER_KANKO_HOUKOKU = 12;
    PUBLIC CONST NEXT_ACTION_KAKUMEI_KOUJO_TOUROKU = 13;
    PUBLIC CONST NEXT_ACTION_GE_COMPLETE = 14;

    public const NEXT_ACTIONS = [
        self::NEXT_ACTION_TAIKYO_UKETUKE => '退去受付',
        self::NEXT_ACTION_CANCELLATION => '解約日',
        self::NEXT_ACTION_TAIKYO => '退去日',
        self::NEXT_ACTION_GENPUKU_MITSUMORI_RECIEVED => '下代',
        self::NEXT_ACTION_TSUDEN => '通電',
        self::NEXT_ACTION_TENANT_CHARGE_CONFIRMED => '借主負担',
        self::NEXT_ACTION_GENPUKU_TEIAN => '貸主提案',
        self::NEXT_ACTION_GENPUKU_TEIAN_KYODAKU => '貸主承諾',
        self::NEXT_ACTION_GENPUKU_KOUJI_HACHU => '発注',
        self::NEXT_ACTION_KANKO_YOTEI => '完工予定',
        self::NEXT_ACTION_KANKO_JYUSHIN => '完工受信',
        self::NEXT_ACTION_OWNER_KANKO_HOUKOKU => '完工報告',
        self::NEXT_ACTION_KAKUMEI_KOUJO_TOUROKU => '革命控除',
        self::NEXT_ACTION_GE_COMPLETE => '完了',
    ];

    protected $table = 'ge_progresses';

    protected $guarded = [
        'id'
    ];

    public function progress()
    {
        return $this->belongsTo(Progress::class);
    }
}
