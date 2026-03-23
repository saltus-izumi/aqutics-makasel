<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use App\Models\Progress;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnProgress extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    // 申込人種別
    public const APPLICANT_TYPE_INDIVIDUAL = 1;
    public const APPLICANT_TYPE_CORPORATE = 2;

    // 審査結果
    public const SCREENING_RESULT_APPROVED = 1;              // 承認
    public const SCREENING_RESULT_CONDITIONAL_APPROVAL = 2;  // 条件付き承認
    public const SCREENING_RESULT_REJECTED = 3;              // 否決

    // 保証ステータス
    public const GUARANTEE_COMPANY_STATUS_CORPORATE_EXEMPT = 1; // 法人除外
    public const GUARANTEE_COMPANY_STATUS_JOINT_GUARANTOR = 2;  // 連帯保証

    // 火災保険ステータス
    public const FIRE_INSURANCE_STATUS_CORPORATE_EXEMPT = 1;    // 法人除外
    public const FIRE_INSURANCE_STATUS_INDIVIDUAL = 2;          // 個人加入

    public const NEXT_ACTION_APPLICATION = 1;
    public const NEXT_ACTION_GUARANTEE_SCREENING = 2;
    public const NEXT_ACTION_WP_SCREENING = 3;
    public const NEXT_ACTION_OWNER_REPORTED = 4;
    public const NEXT_ACTION_OWNER_APPROVED = 5;
    public const NEXT_ACTION_START_DATE_CONFIRMED = 6;
    public const NEXT_ACTION_KEY_REQUESTED = 7;
    public const NEXT_ACTION_INVOICE_ISSUED = 8;
    public const NEXT_ACTION_CONTRACT_SENT = 9;
    public const NEXT_ACTION_CONTRACT_PAYMENT = 10;
    public const NEXT_ACTION_CONTRACT_COLLECTED = 11;
    public const NEXT_ACTION_ELECTRICITY_CANCELLATION = 12;
    public const NEXT_ACTION_KEY_HANDOVER = 13;
    public const NEXT_ACTION_DOCUMENTS_ARCHIVED = 14;
    public const NEXT_ACTION_COMPLETION_REPORTED = 15;
    public const NEXT_ACTION_COMPLETED = 16;
    public const NEXT_ACTION_RE_PROPOSED = 17;
    public const NEXT_ACTION_CANCEL = 18;

    public const NEXT_ACTIONS = [
        self::NEXT_ACTION_APPLICATION => '申込日',
        self::NEXT_ACTION_GUARANTEE_SCREENING => '保証審査',
        self::NEXT_ACTION_WP_SCREENING => 'WP審査',
        self::NEXT_ACTION_OWNER_REPORTED => 'OWN報告',
        self::NEXT_ACTION_OWNER_APPROVED => 'OWN承諾',
        self::NEXT_ACTION_START_DATE_CONFIRMED => '始期日確定日',
        self::NEXT_ACTION_KEY_REQUESTED => '鍵依頼日',
        self::NEXT_ACTION_INVOICE_ISSUED => '請求発行',
        self::NEXT_ACTION_CONTRACT_SENT => '契約発送',
        self::NEXT_ACTION_CONTRACT_PAYMENT => '契約入金',
        self::NEXT_ACTION_CONTRACT_COLLECTED => '契約回収',
        self::NEXT_ACTION_ELECTRICITY_CANCELLATION => '電気解約',
        self::NEXT_ACTION_KEY_HANDOVER => '鍵渡し',
        self::NEXT_ACTION_DOCUMENTS_ARCHIVED => '書類格納',
        self::NEXT_ACTION_COMPLETION_REPORTED => '完了報告',
        self::NEXT_ACTION_COMPLETED => '完了日',
        self::NEXT_ACTION_RE_PROPOSED => '再提案',
        self::NEXT_ACTION_CANCEL => 'キャンセル',
    ];

    protected $table = 'en_progresses';

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
            'start_date' => 'date',
            'application_date' => 'date',
            'guarantee_screening_date' => 'date',
            'wp_screening_date' => 'date',
            'owner_reported_date' => 'date',
            'owner_approved_date' => 'date',
            'start_date_confirmed_date' => 'date',
            'key_requested_date' => 'date',
            'invoice_issued_date' => 'date',
            'contract_sent_date' => 'date',
            'contract_payment_date' => 'date',
            'contract_collected_date' => 'date',
            'electricity_cancellation_date' => 'date',
            'key_handover_date' => 'date',
            'documents_archived_date' => 'date',
            'completion_reported_date' => 'date',
            'completed_date' => 'date',
            'desired_contract_date' => 'date',
        ];
    }

    public function enProgressIndividualApplicant()
    {
        return $this->hasOne(EnProgressIndividualApplicant::class);
    }

    public function enProgressCorporateApplicant()
    {
        return $this->hasOne(EnProgressCorporateApplicant::class);
    }

    public function enProgressOccupants()
    {
        return $this->hasMany(EnProgressOccupants::class);
    }

    public function enProgressEmergencyContact()
    {
        return $this->hasOne(EnProgressEmergencyContact::class);
    }

    public function progress()
    {
        return $this->belongsTo(Progress::class);
    }

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    public function responsibleUser()
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function executorUser()
    {
        return $this->belongsTo(User::class, 'executor_user_id');
    }

    public function firstEnProgressOccupant()
    {
        return $this->hasOne(EnProgressOccupants::class)
            ->ofMany('occupant_seq', 'min');
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

        if ($this?->application_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_APPLICATION;

        } elseif ($this?->guarantee_screening_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_GUARANTEE_SCREENING;

        } elseif ($this?->wp_screening_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_WP_SCREENING;

        } elseif ($this?->owner_reported_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_OWNER_REPORTED;

        } elseif ($this?->owner_approved_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_OWNER_APPROVED;

        } elseif ($this?->start_date_confirmed_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_START_DATE_CONFIRMED;

        } elseif ($this?->key_requested_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_KEY_REQUESTED;

        } elseif ($this?->invoice_issued_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_INVOICE_ISSUED;

        } elseif ($this?->contract_sent_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_CONTRACT_SENT;

        } elseif ($this?->contract_payment_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_CONTRACT_PAYMENT;

        } elseif ($this?->contract_collected_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_CONTRACT_COLLECTED;

        } elseif ($this?->electricity_cancellation_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_ELECTRICITY_CANCELLATION;

        } elseif ($this?->key_handover_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_KEY_HANDOVER;

        } elseif ($this?->documents_archived_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_DOCUMENTS_ARCHIVED;

        } elseif ($this?->completion_reported_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_COMPLETION_REPORTED;

        } elseif ($this?->completed_date_state === 0) {
            $this->next_action = self::NEXT_ACTION_COMPLETED;
        }
    }

    public function getApplicantAttribute()
    {
        return $this->applicant_type === self::APPLICANT_TYPE_INDIVIDUAL
            ? $this->enProgressIndividualApplicant
            : $this->enProgressCorporateApplicant;
    }

    public function scopeWhereApplicantKeyword($query, string $keyword)
    {
        $keyword = trim($keyword);

        return $query->where(function ($q) use ($keyword) {
            $q->where(function ($qq) use ($keyword) {
                $qq->where('applicant_type', self::APPLICANT_TYPE_INDIVIDUAL)
                ->whereHas('enProgressIndividualApplicant', function ($a) use ($keyword) {
                    $a->where('last_name', 'like', "%{$keyword}%")
                        ->orWhere('first_name', 'like', "%{$keyword}%");
                });
            })->orWhere(function ($qq) use ($keyword) {
                $qq->where('applicant_type', self::APPLICANT_TYPE_CORPORATE)
                ->whereHas('enProgressCorporateApplicant', function ($a) use ($keyword) {
                    $a->where('company_name', 'like', "%{$keyword}%");
                });
            });
        });
    }

}
