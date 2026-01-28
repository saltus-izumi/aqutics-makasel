<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category1Master extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    public const INQUIRY = '1';
    public const CANCEL = '2';
    public const CANCEL_INQUIRY = '3';
    public const CLAIM_HARD = '4';
    public const CLAIM_SOFT = '5';
    public const OTHER = '6';
}
