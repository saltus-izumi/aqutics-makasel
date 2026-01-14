<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OperationFile extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    public const FILE_KIND_RETAIL_ESTIMATE = 1;
    public const FILE_KIND_WHOLESALE_ESTIMATE = 2;
    public const FILE_KIND_PURCHASE_ORDER = 3;
    public const FILE_KIND_SITE_REPORT = 4;
    public const FILE_KIND_COMPLETION_PHOTO = 5;
    public const FILE_KIND_OTHER = 6;

    public const FILE_KIND = [
        self::FILE_KIND_RETAIL_ESTIMATE => '上代見積り',
        self::FILE_KIND_WHOLESALE_ESTIMATE => '下代見積もり',
        self::FILE_KIND_PURCHASE_ORDER => '発注書',
        self::FILE_KIND_SITE_REPORT => '現調報告書',
        self::FILE_KIND_COMPLETION_PHOTO => '完工写真',
        self::FILE_KIND_OTHER => 'その他ファイル',
    ];

    protected $guarded = [
        'id'
    ];

}
