<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeProgressFile extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    public const FILE_KIND_RETAIL_ESTIMATE = '1';           // 上代見積り
    public const FILE_KIND_LOWER_ESTIMATE = '2';            // 下代見積り
    public const FILE_KIND_PURCHASE_ORDER = '3';            // 発注書
    public const FILE_KIND_ON_SITE_INSPECTION_REPORT = '4'; // 現調報告書
    public const FILE_KIND_COMPLETION_PHOTO = '5';          // 完工写真

    protected $guarded = [
        'id'
    ];
}
