<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;

class SummaryPeriod extends Model
{
    use RecordsUserStamps;

    protected $table = 'summary_periods';

    protected $guarded = [
        'id',
    ];

    public const CREATED_AT = 'created';
    public const UPDATED_AT = 'modified';

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }
}
