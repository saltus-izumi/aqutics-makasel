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
            'ge_application_date' => 'date',                    // 原復申込日
            'genpuku_shiryou_soushin_date' => 'date',           // 原復会社資料送信
            'notice_of_intent_to_vacate_date' => 'date',        // 保証会社退去連絡
            'taikyo_yotei_date' => 'date',                      // 退去予定日
            'taikyo_date' => 'date',                            // 退去日
            'genpuku_mitsumori_recieved_date' => 'date',        // 見積書受信日
            'tsuden' => 'date',                                 // 通電
            'tenant_charge_confirmed_date' => 'date',           // 借主負担確定
            'genpuku_teian_date' => 'date',                     // OWN原復提案日
            'genpuku_teian_kyodaku_date' => 'date',             // OWN原復承諾日
            'genpuku_kouji_hachu_date' => 'date',               // 原復発注日
            'kanko_yotei_date' => 'date',                       // 完工予定日
            'kanko_jyushin_date' => 'date',                     // 完工受信日
            'owner_kanko_houkoku_date' => 'date',               // OWN完工報告日
            'kakumei_koujo_touroku_date' => 'date',             // 革命控除登録日
            'ge_complete_date' => 'date',                       // 原復完了日
            'taikyo_uketuke_date' => 'date',                    // 退去受付日
            'kaiyaku_date' => 'date',
            'last_import_date' => 'datetime',                   // 最終取り込み日
            'kaiyaku_cancellation_date' => 'date',              // 解約キャンセル日
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

    public function investmentRoomRedidentHistory()
    {
        return $this->belongsTo(InvestmentRoomResidentHistory::class, 'contractor_no', 'contractor_no');
    }

    public function investmentEmptyRoom()
    {
        return $this->belongsTo(InvestmentEmptyRoom::class);
    }

    public function geProgresses()
    {
        return $this->hasMany(GeProgress::class);
    }

    public function genpukuResponsible()
    {
        return $this->belongsTo(User::class, 'genpuku_responsible_id');
    }

    protected function geNextAction(): Attribute
    {
        return Attribute::get(function () {
            $nextAction = null;

            if (!$this?->taikyo_uketuke_date) {
                $nextAction = GeProgress::NEXT_ACTION_MOVE_OUT_RECEIVED;
            } elseif (!$this?->investmentEmptyRoom?->cancellation_date) {
                $nextAction = GeProgress::NEXT_ACTION_CANCELLATION;
            } elseif ($this?->taikyo_date_state === 0) {
                $nextAction = GeProgress::NEXT_ACTION_MOVE_OUT;
            } elseif (!$this?->genpuku_mitsumori_recieved_date) {
                $nextAction = GeProgress::NEXT_ACTION_COST_RECEIVED;
            } elseif ($this?->tsuden_state === 0) {
                $nextAction = GeProgress::NEXT_ACTION_POWER_ACTIVATION;
            } elseif ($this?->tenant_charge_confirmed_date_state === 0) {
                $nextAction = GeProgress::NEXT_ACTION_TENANT_BURDEN_CONFIRMED;
            } elseif ($this?->genpuku_teian_date_state === 0) {
                $nextAction = GeProgress::NEXT_ACTION_OWNER_PROPOSED;
            } elseif ($this?->genpuku_teian_kyodaku_date_state === 0) {
                $nextAction = GeProgress::NEXT_ACTION_OWNER_APPROVED;
            } elseif ($this?->genpuku_kouji_hachu_date_state === 0) {
                $nextAction = GeProgress::NEXT_ACTION_ORDERED;
            } elseif ($this?->kanko_yotei_date_state === 0) {
                $nextAction = GeProgress::NEXT_ACTION_COMPLETION_SCHEDULED;
            } elseif ($this?->kanko_jyushin_date_state === 0) {
                $nextAction = GeProgress::NEXT_ACTION_COMPLETION_RECEIVED;
            } elseif ($this?->owner_kanko_houkoku_date_state === 0) {
                $nextAction = GeProgress::NEXT_ACTION_COMPLETION_REPORTED;
            } elseif ($this?->kakumei_koujo_touroku_date_state === 0) {
                $nextAction = GeProgress::NEXT_ACTION_KAKUMEI_REGISTERED;
            } elseif ($this?->ge_complete_date_state === 0) {
                $nextAction = GeProgress::NEXT_ACTION_COMPLETED;
            }

            return $nextAction;
        });
    }

}
