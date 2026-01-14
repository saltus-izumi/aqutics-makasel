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

}
