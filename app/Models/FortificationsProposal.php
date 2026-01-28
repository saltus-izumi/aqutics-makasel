<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FortificationsProposal extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    protected $guarded = [
        'id'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'suggestion_date' => 'date',
            'accepted_date' => 'date',
            'unsolicited_date' => 'date',
            'completed_date' => 'date',
        ];
    }
}
