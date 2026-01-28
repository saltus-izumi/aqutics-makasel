<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category3Master extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    public const KIND_TE = '1';
    public const KIND_EN = '2';
    public const KIND_LE = '3';
    public const KIND_KEIRI = '4';
}
