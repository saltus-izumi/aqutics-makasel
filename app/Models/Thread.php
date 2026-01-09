<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Thread extends Model
{
    use SoftDeletes;

    public const STATUS_DRAFT = 1;
    public const STATUS_PROPOSED = 2;
    public const STATUS_REPROPOSED = 3;
    public const STATUS_OWNER_APPROVED = 4;
    public const STATUS_OWNER_REJECTED = 5;
    public const STATUS_CANCELED = 9;

    public const OPERATION_GROUPS = [
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
