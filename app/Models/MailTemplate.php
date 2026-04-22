<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailTemplate extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    protected $guarded = [
        'id'
    ];

    const MAIL_KIND_GE_PROGRESS_REQUEST_VISIT = 1;     // 原復プロセスー立会依頼
    const MAIL_KIND_GE_PROGRESS_VISIT_DONE = 2;        // 原復プロセスー立会完了
    const MAIL_KIND_GE_PROGRESS_CONSTRUCTION_DONE = 3; // 原復プロセスー工事完了
    const MAIL_KIND_GE_PROGRESS_ORDER_PLACED = 4;      // 原復プロセスー原復会社発注
    const MAIL_KIND = [
        self::MAIL_KIND_GE_PROGRESS_REQUEST_VISIT => '原復プロセスー立会依頼',
        self::MAIL_KIND_GE_PROGRESS_VISIT_DONE => '原復プロセスー立会完了',
        self::MAIL_KIND_GE_PROGRESS_CONSTRUCTION_DONE => '原復プロセスー工事完了',
        self::MAIL_KIND_GE_PROGRESS_ORDER_PLACED => '原復プロセスー原復会社発注',
    ];

}
