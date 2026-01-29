<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Progress extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    protected $table = 'progresses';

    public const LAST_IMPORT_KIND_NEW = 1;
    public const LAST_IMPORT_KIND_UPDATE = 2;
    public const LAST_IMPORT_KIND = [
        self::LAST_IMPORT_KIND_NEW => '新規',
        self::LAST_IMPORT_KIND_UPDATE => '更新',
    ];

    protected $guarded = [
        'id'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ge_application_date' => 'date',
            'ge_complete_date' => 'date',
            'genpuku_shiryou_soushin_date' => 'date',
            'notice_of_intent_to_vacate_date' => 'date',
            'taikyo_yotei_date' => 'date',
            'taikyo_date' => 'date',
            'genpuku_mitsumori_recieved_date' => 'date',
            'tsuden' => 'date',



            'taikyo_uketuke_date' => 'date',
            'kaiyaku_date' => 'date',
            'last_import_date' => 'datetime',
        ];
    }

    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }

    public function investmentRoom()
    {
        return $this->belongsTo(InvestmentRoom::class, 'investment_room_uid', 'id');
    }

    public function investmentEmptyRoom()
    {
        return $this->belongsTo(InvestmentEmptyRoom::class);
    }
}
