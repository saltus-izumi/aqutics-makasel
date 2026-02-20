<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use App\Models\Progress;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeProgress extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    public const NEXT_ACTION_MOVE_OUT_RECEIVED = 1;
    public const NEXT_ACTION_CANCELLATION = 2;
    public const NEXT_ACTION_MOVE_OUT = 3;
    public const NEXT_ACTION_COST_RECEIVED = 4;
    public const NEXT_ACTION_POWER_ACTIVATION = 5;
    public const NEXT_ACTION_TENANT_BURDEN_CONFIRMED = 6;
    public const NEXT_ACTION_OWNER_PROPOSED = 7;
    public const NEXT_ACTION_OWNER_APPROVED = 8;
    public const NEXT_ACTION_ORDERED = 9;
    public const NEXT_ACTION_COMPLETION_SCHEDULED = 10;
    public const NEXT_ACTION_COMPLETION_RECEIVED = 11;
    public const NEXT_ACTION_COMPLETION_REPORTED = 12;
    public const NEXT_ACTION_KAKUMEI_REGISTERED = 13;
    public const NEXT_ACTION_COMPLETED = 14;
    public const NEXT_ACTION_RE_PROPOSED = 15;
    public const NEXT_ACTION_CANCEL = 16;

    public const NEXT_ACTIONS = [
        self::NEXT_ACTION_MOVE_OUT_RECEIVED => '退去受付',
        self::NEXT_ACTION_CANCELLATION => '解約日',
        self::NEXT_ACTION_MOVE_OUT => '退去日',
        self::NEXT_ACTION_COST_RECEIVED => '下代',
        self::NEXT_ACTION_POWER_ACTIVATION => '通電',
        self::NEXT_ACTION_TENANT_BURDEN_CONFIRMED => '借主負担',
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
            'move_out_received_date' => 'date',
            'move_out_date' => 'date',
            'cost_received_date' => 'date',
            'power_activation_date' => 'date',
            'tenant_burden_confirmed_date' => 'date',
            'owner_proposed_date' => 'date',
            'owner_approved_date' => 'date',
            'ordered_date' => 'date',
            'completion_scheduled_date' => 'date',
            'completion_received_date' => 'date',
            'completion_reported_date' => 'date',
            'kakumei_registered_date' => 'date',
            'completed_date' => 'date',
            'kaiyaku_cancellation_date' => 'date',
            'move_out_report_date' => 'date',
            'transfer_due_date' => 'date:Y/m/d',
        ];
    }

    public function progress()
    {
        return $this->belongsTo(Progress::class);
    }

    public function responsibleUser()
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
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
    public function lowerEstimateFiles()
    {
        return $this->hasMany(GeProgressFile::class)
            ->where('file_kind', GeProgressFile::FILE_KIND_LOWER_ESTIMATE);
    }

    // 立会写真ファイル
    public function walkthroughPhotoFiles()
    {
        return $this->hasMany(GeProgressFile::class)
            ->where('file_kind', GeProgressFile::FILE_KIND_WALKTHROUGH_PHOTO);
    }

    // 上代見積もりファイル
    public function retailEstimateFiles()
    {
        return $this->hasMany(GeProgressFile::class)
            ->where('file_kind', GeProgressFile::FILE_KIND_RETAIL_ESTIMATE);
    }

    // その他完工写真
    public function completionPhotoFiles()
    {
        return $this->hasMany(GeProgressFile::class)
            ->where('file_kind', GeProgressFile::FILE_KIND_COMPLETION_PHOTO);
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

        if (!$this?->move_out_received_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_MOVE_OUT_RECEIVED;

        } elseif (!$this?->progress?->investmentEmptyRoom?->cancellation_date) {
            $this->next_action = self::NEXT_ACTION_CANCELLATION;

        } elseif ($this?->move_out_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_MOVE_OUT;

        } elseif ($this?->cost_received_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_COST_RECEIVED;

        } elseif ($this?->power_activation_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_POWER_ACTIVATION;

        } elseif ($this?->tenant_burden_confirmed_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_TENANT_BURDEN_CONFIRMED;

        } elseif ($this?->owner_proposed_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_OWNER_PROPOSED;

        } elseif ($this?->owner_approved_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_OWNER_APPROVED;

        } elseif ($this?->ordered_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_ORDERED;

        } elseif ($this?->completion_scheduled_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_COMPLETION_SCHEDULED;

        } elseif ($this?->completion_received_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_COMPLETION_RECEIVED;

        } elseif ($this?->completion_reported_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_COMPLETION_REPORTED;

        } elseif ($this?->kakumei_registered_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_KAKUMEI_REGISTERED;

        } elseif ($this?->completed_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_COMPLETED;
        }
    }

}
