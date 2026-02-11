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

    public const NEXT_ACTION_TAIKYO_UKETUKE = 1;
    public const NEXT_ACTION_CANCELLATION = 2;
    public const NEXT_ACTION_TAIKYO = 3;
    public const NEXT_ACTION_GENPUKU_MITSUMORI_RECIEVED = 4;
    public const NEXT_ACTION_TSUDEN = 5;
    public const NEXT_ACTION_TENANT_CHARGE_CONFIRMED = 6;
    public const NEXT_ACTION_GENPUKU_TEIAN = 7;
    public const NEXT_ACTION_GENPUKU_TEIAN_KYODAKU = 8;
    public const NEXT_ACTION_GENPUKU_KOUJI_HACHU = 9;
    public const NEXT_ACTION_KANKO_YOTEI = 10;
    public const NEXT_ACTION_KANKO_JYUSHIN = 11;
    public const NEXT_ACTION_OWNER_KANKO_HOUKOKU = 12;
    public const NEXT_ACTION_KAKUMEI_KOUJO_TOUROKU = 13;
    public const NEXT_ACTION_GE_COMPLETE = 14;

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

    public const IS_PROPER_WORK_BURDEN_APPROVED = 1;
    public const IS_PROPER_WORK_BURDEN_REJECTED = 2;
    public const IS_PROPER_WORK_BURDEN = [
        self::IS_PROPER_WORK_BURDEN_APPROVED => '承諾',
        self::IS_PROPER_WORK_BURDEN_REJECTED => '差し戻し',
    ];

    public const IS_PROPER_PRICE_APPROVED = 1;
    public const IS_PROPER_PRICE_REJECTED = 2;
    public const IS_PROPER_PRICE = [
        self::IS_PROPER_PRICE_APPROVED => '承諾',
        self::IS_PROPER_PRICE_REJECTED => '差し戻し',
    ];


    protected $table = 'ge_progresses';

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
            'move_out_report_date' => 'date',
            'transfer_due_date' => 'date:Y/m/d',
        ];
    }

    public function progress()
    {
        return $this->belongsTo(Progress::class);
    }

    public function executorUser()
    {
        return $this->belongsTo(User::class, 'executor_user_id');
    }

    public function geProgressFiles()
    {
        return $this->hasMany(GeProgressFile::class);
    }

    // STEP1ファイル
    public function step1Files()
    {
        return $this->hasMany(GeProgressFile::class)
            ->where('file_kind', GeProgressFile::FILE_KIND_STEP1);
    }

    // 退去時清算書ファイル
    public function moveOutSettlementFiles()
    {
        return $this->hasMany(GeProgressFile::class)
            ->where('file_kind', GeProgressFile::FILE_KIND_MOVE_OUT_SETTLEMENT);
    }

    // 下代見積もりファイル
    public function costEstimateFiles()
    {
        return $this->hasMany(GeProgressFile::class)
            ->where('file_kind', GeProgressFile::FILE_KIND_COST_ESTIMATE);
    }

    // 立会写真ファイル
    public function walkthroughPhotoFiles()
    {
        return $this->hasMany(GeProgressFile::class)
            ->where('file_kind', GeProgressFile::FILE_KIND_WALKTHROUGH_PHOTO);
    }

    // その他完工写真
    public function otherCompletionPhotoFiles()
    {
        return $this->hasMany(GeProgressFile::class)
            ->where('file_kind', GeProgressFile::FILE_KIND_OTHER_COMPLETION_PHOTO);
    }

}
