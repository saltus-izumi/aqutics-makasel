<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Thread extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    public const THREAD_TYPE_OPERATION = 1;
    public const THREAD_TYPE_CHAT = 2;

    public const THREAD_TYPE = [
        self::THREAD_TYPE_OPERATION => 'オペレーション',
        self::THREAD_TYPE_CHAT => 'チャット',
    ];

    public const STATUS_DRAFT = 1;
    public const STATUS_PROPOSED = 2;
    public const STATUS_REPROPOSED = 3;
    public const STATUS_OWNER_APPROVED = 4;
    public const STATUS_OWNER_REJECTED = 5;
    public const STATUS_CANCELED = 9;

    public const STATUS = [
        self::STATUS_DRAFT => '下書き',
        self::STATUS_PROPOSED => '提案済',
        self::STATUS_REPROPOSED => '再提案',
        self::STATUS_OWNER_APPROVED => 'オーナー承諾済み',
        self::STATUS_OWNER_REJECTED => 'オーナー却下',
        self::STATUS_CANCELED => '中止',
    ];

    public const OWNER_STATUS = [
        self::STATUS_DRAFT => '下書き',
        self::STATUS_PROPOSED => '進行中',
        self::STATUS_REPROPOSED => '進行中',
        self::STATUS_OWNER_APPROVED => '承諾済',
        self::STATUS_OWNER_REJECTED => '却下',
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
            'last_post_at' => 'datetime',
            'first_post_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }

    public function investmentRoom()
    {
        return $this->belongsTo(InvestmentRoom::class, 'investment_room_id', 'id');
    }


    public function threadMessages()
    {
        return $this->hasMany(ThreadMessage::class);
    }

    public function operations()
    {
        return $this->hasMany(Operation::class);
    }

    protected function lastMessage(): Attribute
    {
        return Attribute::get(function () {
            if ($this->threadMessages) {
                $threadMessages = $this->threadMessages;

                if ($threadMessages instanceof \Illuminate\Support\Collection) {
                    return $threadMessages->last();
                }
            }

            return null;
        });
    }

    protected function firstOperation(): Attribute
    {
        return Attribute::get(function () {
            if ($this->operations) {
                $operations = $this->operations;

                if ($operations instanceof \Illuminate\Support\Collection) {
                    return $operations->first();
                }
            } elseif ($this->threadMessages) {
                $messages = $this->threadMessages;

                if ($messages instanceof \Illuminate\Support\Collection) {
                    $message = $messages->first(fn ($message) => $message->operation);
                    return $message?->operation;
                }
            }

            return null;
        });
    }

    protected function lastOperation(): Attribute
    {
        return Attribute::get(function () {
            if ($this->operations) {
                $operations = $this->operations;

                if ($operations instanceof \Illuminate\Support\Collection) {
                    return $operations->last();
                }
            } elseif ($this->threadMessages) {
                $messages = $this->threadMessages;

                if ($messages instanceof \Illuminate\Support\Collection) {
                    $message = $messages->last(fn ($message) => $message->operation);
                    return $message?->operation;
                }
            }

            return null;
        });
    }

}
