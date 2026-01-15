<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operation extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    public const STATUS_DRAFT = 1;
    public const STATUS_IN_PROGRESS = 2;
    public const STATUS_CONFIRMED = 3;
    public const STATUS_APPROVED = 4;
    public const STATUS_REJECTED = 5;
    public const STATUS_CANCELED = 9;

    public const STATUS = [
        self::STATUS_DRAFT => '下書き',
        self::STATUS_IN_PROGRESS => '進行中（送信済み）',
        self::STATUS_CONFIRMED => '確認済み（既読）',
        self::STATUS_APPROVED => '承諾',
        self::STATUS_REJECTED => '拒否',
        self::STATUS_CANCELED => '中止',
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
            'sent_at' => 'datetime',
            'read_at' => 'datetime',
            'replied_at' => 'datetime',
        ];
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    public function operationKind()
    {
        return $this->belongsTo(OperationKind::class);
    }

    public function operationTemplate()
    {
        return $this->belongsTo(OperationTemplate::class);
    }

    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }

    public function investmentRoom()
    {
        return $this->belongsTo(InvestmentRoom::class, 'investment_room_id', 'id');
    }

    public function threadMessage()
    {
        return $this->belongsTo(ThreadMessage::class, 'thread_message_id', 'id');
    }

}
