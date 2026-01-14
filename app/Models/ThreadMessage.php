<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThreadMessage extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

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

}
