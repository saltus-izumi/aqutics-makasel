<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KpiType extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    protected $guarded = [
        'id'
    ];

    public const DEPARTMENT_LE = 1;      // LE
    public const DEPARTMENT_EN = 2;      // EN
    public const DEPARTMENT_TE = 3;      // TE
    public const DEPARTMENTS = [
        self::DEPARTMENT_LE => 'LE',
        self::DEPARTMENT_EN => 'EN',
        self::DEPARTMENT_TE => 'TE',
    ];

    const BASE_FIELD_NAME = [
        'taikyo_uketuke_date' => '解約受付日',
        'cancellation_date' => '解約日',
        'taikyo_date' => '退去日',
        'mousikomi_date' => '申込日',
        'gencho_date' => '現調日',
        'nyuuden_date' => '入電日',
    ];
}
