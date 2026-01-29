<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;

class InvestmentEmptyRoom extends Model
{
    use RecordsUserStamps;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cancellation_date' => 'date',
        ];
    }

    /**
     * この部屋が属する物件を取得
     */
    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }
}
