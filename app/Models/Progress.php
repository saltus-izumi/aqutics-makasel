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

    protected function geStatus(): Attribute
    {
        return Attribute::get(function () {
            $status = '';

            if (!$this?->taikyo_uketuke_date) {
                $status = '退去受付';
            } elseif (!$this?->investmentEmptyRoom?->cancellation_date) {
                $status = '解約日';
            } elseif (!$this?->taikyo_date) {
                $status = '退去日';
            } elseif (!$this?->genpuku_mitsumori_recieved_date) {
                $status = '下代';
            } elseif (!$this?->tsuden) {
                $status = '通電';
            } elseif (!$this?->tenant_charge_confirmed_date) {
                $status = '借主負担';
            } elseif (!$this?->genpuku_teian_date) {
                $status = '貸主提案';
            } elseif (!$this?->genpuku_teian_kyodaku_date) {
                $status = '貸主承諾';
            } elseif (!$this?->genpuku_kouji_hachu_date) {
                $status = '発注';
            } elseif (!$this?->kanko_yotei_date) {
                $status = '完工予定';
            } elseif (!$this?->kanko_jyushin_date) {
                $status = '完工受信';
            } elseif (!$this?->owner_kanko_houkoku_date) {
                $status = '完工報告';
            } elseif (!$this?->kakumei_koujo_touroku_date) {
                $status = '革命控除';
            } elseif (!$this?->ge_complete_date) {
                $status = '完了';
            }

            // if ($this?->ge_complete_date) {
            //     $status = '完了';
            // } elseif ($this?->kakumei_koujo_touroku_date) {
            //     $status = '革命控除';
            // } elseif ($this?->owner_kanko_houkoku_date) {
            //     $status = '完工報告';
            // } elseif ($this?->kanko_jyushin_date) {
            //     $status = '完工受信';
            // } elseif ($this?->kanko_yotei_date) {
            //     $status = '完工予定';
            // } elseif ($this?->genpuku_kouji_hachu_date) {
            //     $status = '発注';
            // } elseif ($this?->genpuku_teian_kyodaku_date) {
            //     $status = '貸主承諾';
            // } elseif ($this?->genpuku_teian_date) {
            //     $status = '貸主提案';
            // } elseif ($this?->tenant_charge_confirmed_date) {
            //     $status = '借主負担';
            // } elseif ($this?->tsuden) {
            //     $status = '通電';
            // } elseif ($this?->genpuku_mitsumori_recieved_date) {
            //     $status = '下代';
            // } elseif ($this?->taikyo_date) {
            //     $status = '退去日';
            // } elseif ($this?->investmentEmptyRoom?->cancellation_date) {
            //     $status = '解約日';
            // } elseif ($this?->taikyo_uketuke_date) {
            //     $status = '退去受付';
            // }

            return $status;
        });
    }

}
