<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;

class InvestmentRoom extends Model
{
    use RecordsUserStamps;

    protected $guarded = [
        'id'
    ];

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

    /****************
    * プロコールからの部屋名でデータを取得する
    *
    * 取得ルール
    *   以下の順で取得を試す
    *   1. そのままの名称で取得
    *   2. 半角変換可能なものを半角変換して検索（－を半角-に変換する)
    ****************/
    public static function getByInvestmentRoomNumberForProcall($investmentId, $investmentRoomNumber) {
        // そのままの名称で検索
        $investmentRoom = self::where('investment_id', $investmentId)
            ->where('investment_room_number', $investmentRoomNumber)
            ->first();
        if ($investmentRoom) return $investmentRoom;

        // 半角変換可能なものを半角に変換して検索
        $investmentRoom = self::where('investment_id', $investmentId)
            ->where('investment_room_number', mb_convert_kana(str_replace('－', '-', $investmentRoomNumber), 'r'))
            ->first();
        if ($investmentRoom) return $investmentRoom;

        return null;
    }

}
