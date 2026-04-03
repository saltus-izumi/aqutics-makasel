<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeProgress extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    protected $table = 'te_progresses';

    public const LAST_IMPORT_KIND_NEW = 1;
    public const LAST_IMPORT_KIND_UPDATE = 2;
    public const LAST_IMPORT_KIND = [
        self::LAST_IMPORT_KIND_NEW => '新規',
        self::LAST_IMPORT_KIND_UPDATE => '更新',
    ];

    public const NEXT_ACTION_NYUUDEN = 1;
    public const NEXT_ACTION_GENCHO = 2;
    public const NEXT_ACTION_COST_RECEIVED = 3;
    public const NEXT_ACTION_OWNER_PROPOSED = 4;
    public const NEXT_ACTION_OWNER_APPROVED = 5;
    public const NEXT_ACTION_ORDERED = 6;
    public const NEXT_ACTION_COMPLETION_SCHEDULED = 7;
    public const NEXT_ACTION_COMPLETION_RECEIVED = 8;
    public const NEXT_ACTION_COMPLETION_REPORTED = 9;
    public const NEXT_ACTION_KAKUMEI_REGISTERED = 10;
    public const NEXT_ACTION_COMPLETED = 11;
    public const NEXT_ACTION_RE_PROPOSED = 12;
    public const NEXT_ACTION_CANCEL = 13;

    public const NEXT_ACTIONS = [
        self::NEXT_ACTION_NYUUDEN => '入電',
        self::NEXT_ACTION_GENCHO => '現調予定',
        self::NEXT_ACTION_COST_RECEIVED => '下代',
        self::NEXT_ACTION_OWNER_PROPOSED => '貸主提案',
        self::NEXT_ACTION_OWNER_APPROVED => '貸主承諾',
        self::NEXT_ACTION_ORDERED => '発注',
        self::NEXT_ACTION_COMPLETION_SCHEDULED => '完工予定',
        self::NEXT_ACTION_COMPLETION_RECEIVED => '完工受信',
        self::NEXT_ACTION_COMPLETION_REPORTED => '完工報告',
        self::NEXT_ACTION_KAKUMEI_REGISTERED => '革命控除',
        self::NEXT_ACTION_COMPLETED => '完了',
        self::NEXT_ACTION_RE_PROPOSED => '再提案',
        self::NEXT_ACTION_CANCEL => 'キャンセル',
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
            'nyuuden_date' => 'date',
            'complete_date' => 'date',
            'last_import_date' => 'datetime',
            'cost_received_date' => 'date',
            'gencho_date' => 'date',
            'own_suggestion_date' => 'date',
            'own_consent_date' => 'date',
            'pc_hachu_date' => 'date',
            'kanko_yotei_date' => 'date',
            'pc_kanko_receive_date' => 'date',
            'pc_kanko_report_date' => 'date',
            'kakumei_koujo_date' => 'date',
            'complete_date' => 'date',
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

    public function investmentRoomResidentHistory()
    {
        return $this->belongsTo(InvestmentRoomResidentHistory::class, 'contractor_no', 'contractor_no');
    }

    public function investmentRoomResident()
    {
        return $this->belongsTo(InvestmentRoomResident::class, 'contractor_no', 'contractor_no');
    }

    public function responsibleUser()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function executorUser()
    {
        return $this->belongsTo(User::class, 'executor_user_id');
    }

    public function category1Master()
    {
        return $this->belongsTo(Category1Master::class, 'category1_master_id', 'id');
    }

    public function category2Master()
    {
        return $this->belongsTo(Category2Master::class, 'category2_master_id', 'id');
    }

    public function category3Master()
    {
        return $this->belongsTo(Category3Master::class, 'category3_master_id', 'id');
    }

    public function tradingCompany1() {
        return $this->belongsTo(TradingCompany::class, 'trading_company_1_id', 'id');
    }

    public function tradingCompany2() {
        return $this->belongsTo(TradingCompany::class, 'trading_company_2_id', 'id');
    }

    public function tradingCompany3() {
        return $this->belongsTo(TradingCompany::class, 'trading_company_3_id', 'id');
    }

    public function isTodayImport(): Attribute
    {
        return Attribute::get(function (): bool {
            return $this->last_import_date?->isSameDay(now()) ?? false;
        });
    }

    public function lastTradingCompany(): Attribute
    {
        return Attribute::get(function () {
            return ($this->tradingCompany3 ?? $this->tradingCompany2 ?? $this->tradingCompany1 ?? null);
        });
    }


    // 上代見積り
    public function retailEstimateFiles()
    {
        return $this->hasMany(TeProgressFile::class)
            ->where('file_kind', TeProgressFile::FILE_KIND_RETAIL_ESTIMATE);
    }

    // 下代見積り
    public function lowerEstimateFiles()
    {
        return $this->hasMany(TeProgressFile::class)
            ->where('file_kind', TeProgressFile::FILE_KIND_LOWER_ESTIMATE);
    }

    // 発注書
    public function purchaseOrderFiles()
    {
        return $this->hasMany(TeProgressFile::class)
            ->where('file_kind', TeProgressFile::FILE_KIND_PURCHASE_ORDER);
    }

    // 現調報告書
    public function onSiteInspectionReportFiles()
    {
        return $this->hasMany(TeProgressFile::class)
            ->where('file_kind', TeProgressFile::FILE_KIND_ON_SITE_INSPECTION_REPORT);
    }

    // 完工写真
    public function completionPhotoFiles()
    {
        return $this->hasMany(TeProgressFile::class)
            ->where('file_kind', TeProgressFile::FILE_KIND_COMPLETION_PHOTO);
    }

    public function resetNextAction()
    {
        // 終了判定
        if (in_array($this?->next_action, [
            self::NEXT_ACTION_RE_PROPOSED,
            self::NEXT_ACTION_CANCEL,
        ], true)) {
            return;
        }

        if (!$this?->nyuuden_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_NYUUDEN;

        } elseif ($this?->gencho_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_GENCHO;

        } elseif ($this?->cost_received_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_COST_RECEIVED;

        } elseif ($this?->own_suggestion_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_OWNER_PROPOSED;

        } elseif ($this?->own_consent_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_OWNER_APPROVED;

        } elseif ($this?->pc_hachu_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_ORDERED;

        } elseif ($this?->kanko_yotei_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_COMPLETION_SCHEDULED;

        } elseif ($this?->pc_kanko_receive_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_COMPLETION_RECEIVED;

        } elseif ($this?->pc_kanko_report_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_COMPLETION_REPORTED;

        } elseif ($this?->kakumei_koujo_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_KAKUMEI_REGISTERED;

        } elseif ($this?->complete_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_COMPLETED;
        }
    }

}
