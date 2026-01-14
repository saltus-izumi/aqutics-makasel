<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
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

    protected $guarded = [
        'id'
    ];

}
