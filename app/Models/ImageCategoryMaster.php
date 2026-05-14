<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImageCategoryMaster extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    public const CATEGORY_KIND_EXTERIOR = '1';
    public const CATEGORY_KIND_INTERIOR = '2';

    public const SHORT_NAME = [
        self::CATEGORY_KIND_EXTERIOR => '外観',
        self::CATEGORY_KIND_INTERIOR => '内観',
    ];
}
