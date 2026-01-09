<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentRoom extends Model
{
    /**
     * この部屋が属する物件を取得
     */
    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }

    /**
     * 物件に紐づく部屋のオプションを取得
     *
     * @param int|string $investmentId
     * @return array
     */
    public static function getOptionsByInvestment($investmentId): array
    {
        $investmentRooms = self::where('investment_id', $investmentId)
            ->orderBy('investment_room_id', 'asc')
            ->get();

        $options = [];
        if ($investmentRooms) {
            foreach ($investmentRooms as $investmentRoom) {
                $options[$investmentRoom->id] = $investmentRoom->investment_room_number;
            }
        }

        return $options;
    }

}
