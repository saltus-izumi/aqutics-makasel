<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThreadMessage extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    public const MESSAGE_TYPE_OPERATION = 1;
    public const MESSAGE_TYPE_OPERATION_REPLY = 2;
    public const MESSAGE_TYPE_CHAT_MESSAGE = 3;

    public const MESSAGE_TYPE = [
        self::MESSAGE_TYPE_OPERATION => 'オペレーション',
        self::MESSAGE_TYPE_OPERATION_REPLY => 'オペレーション返答',
        self::MESSAGE_TYPE_CHAT_MESSAGE => 'チャットメッセージ',
    ];

    public const SENDER_TYPE_USER = 1;
    public const SENDER_TYPE_OWNER = 2;

    public const SENDER_TYPE = [
        self::SENDER_TYPE_USER => 'アクティスユーザ',
        self::SENDER_TYPE_OWNER => 'オーナー',
    ];

    public const STATUS_DRAFT = 1;
    public const STATUS_SENT = 2;

    public const OPERATION_GROUPS = [
        self::STATUS_DRAFT => '下書き',
        self::STATUS_SENT => '送信済',
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
        ];
    }

    public function senderUser()
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    public function operation()
    {
        return $this->hasOne(Operation::class, 'thread_message_id', 'id');
    }

}
