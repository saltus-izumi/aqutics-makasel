<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
            'tenant_charge_confirmed_date' => 'date',
            'genpuku_teian_date' => 'date',
            'genpuku_teian_kyodaku_date' => 'date',
            'genpuku_kouji_hachu_date' => 'date',
            'kanko_yotei_date' => 'date',
            'kanko_jyushin_date' => 'date',
            'owner_kanko_houkoku_date' => 'date',
            'kakumei_koujo_touroku_date' => 'date',
            'ge_complete_date' => 'date',



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

    public function geProgress()
    {
        return $this->hasOne(GeProgress::class);
    }

    protected function geNextAction(): Attribute
    {
        return Attribute::get(function () {
            $nextAction = null;

            if (!$this?->taikyo_uketuke_date) {
                $nextAction = GeProgress::NEXT_ACTION_TAIKYO_UKETUKE;
            } elseif (!$this?->investmentEmptyRoom?->cancellation_date) {
                $nextAction = GeProgress::NEXT_ACTION_CANCELLATION;
            } elseif ($this?->taikyo_date_state === 0) {
                $nextAction = GeProgress::NEXT_ACTION_TAIKYO;
            } elseif (!$this?->genpuku_mitsumori_recieved_date) {
                $nextAction = GeProgress::NEXT_ACTION_GENPUKU_MITSUMORI_RECIEVED;
            } elseif ($this?->tsuden_state === 0) {
                $nextAction = GeProgress::NEXT_ACTION_TSUDEN;
            } elseif ($this?->tenant_charge_confirmed_date_state === 0) {
                $nextAction = GeProgress::NEXT_ACTION_TENANT_CHARGE_CONFIRMED;
            } elseif ($this?->genpuku_teian_date_state === 0) {
                $nextAction = GeProgress::NEXT_ACTION_GENPUKU_TEIAN;
            } elseif ($this?->genpuku_teian_kyodaku_date_state === 0) {
                $nextAction = GeProgress::NEXT_ACTION_GENPUKU_TEIAN_KYODAKU;
            } elseif ($this?->genpuku_kouji_hachu_date_state === 0) {
                $nextAction = GeProgress::NEXT_ACTION_GENPUKU_KOUJI_HACHU;
            } elseif ($this?->kanko_yotei_date_state === 0) {
                $nextAction = GeProgress::NEXT_ACTION_KANKO_YOTEI;
            } elseif ($this?->kanko_jyushin_date_state === 0) {
                $nextAction = GeProgress::NEXT_ACTION_KANKO_JYUSHIN;
            } elseif ($this?->owner_kanko_houkoku_date_state === 0) {
                $nextAction = GeProgress::NEXT_ACTION_OWNER_KANKO_HOUKOKU;
            } elseif ($this?->kakumei_koujo_touroku_date_state === 0) {
                $nextAction = GeProgress::NEXT_ACTION_KAKUMEI_KOUJO_TOUROKU;
            } elseif ($this?->ge_complete_date_state === 0) {
                $nextAction = GeProgress::NEXT_ACTION_GE_COMPLETE;
            }

            return $nextAction;
        });
    }

}
