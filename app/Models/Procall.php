<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Procall extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    protected $table = 'procalls';

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
            'pr003' => 'datetime',
            'pr032' => 'datetime',
            'pr033' => 'datetime',
        ];
    }

}
