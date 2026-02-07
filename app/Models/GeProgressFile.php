<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use App\Models\Progress;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeProgressFile extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    public const FILE_KIND_STEP1 = 1;                   // ステップ１ファイル
    public const FILE_KIND_MOVE_OUT_SETTLEMENT = 2;     // 退去時清算書
    public const FILE_KIND_COST_ESTIMATE = 3;           // 下代見積もり
    public const FILE_KIND_WALKTHROUGH_PHOTO = 4;       // 立会写真

    protected $table = 'ge_progress_files';

    protected $guarded = [
        'id'
    ];
}
