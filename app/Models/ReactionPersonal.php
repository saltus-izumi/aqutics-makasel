<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;

class ReactionPersonal extends Model
{
    use RecordsUserStamps;

    protected $table = 'reaction_personals';

    protected $guarded = [
        'id',
    ];

    public const CREATED_AT = 'created';
    public const UPDATED_AT = 'modified';

    protected function casts(): array
    {
        return [
            'sampling_date' => 'date',
        ];
    }
}
