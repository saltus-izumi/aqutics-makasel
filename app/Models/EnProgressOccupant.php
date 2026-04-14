<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use App\Models\Progress;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnProgressOccupant extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    // 申込者性別
    public const GENDER_MALE = 1;       // 男性
    public const GENDER_FEMALE = 2;     // 女性

    protected $table = 'en_progress_occupants';

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
            'birth_date' => 'date',
            'user_created_at' => 'datetime',
            'user_updated_at' => 'datetime',
            'user_deleted_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    protected function fullName(): Attribute
    {
        return Attribute::get(fn () => $this->last_name . '　' . $this->first_name);
    }

    public function enProgress()
    {
        return $this->belongsTo(EnProgress::class);
    }
}
