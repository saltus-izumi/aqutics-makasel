<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CityRank extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    public static function getOptions(): array
    {
        return self::query()
            ->orderBy('disp_rank', 'asc')
            ->pluck('item_name', 'id')
            ->toArray();
    }
}
