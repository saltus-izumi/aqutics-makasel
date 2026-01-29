<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuggestEmptyRoomNewEquipment extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    public const KIND_CONTRACT = 1;                 // 契約携帯
    public const KIND_INVESTMENT_TYPE = 2;          // 物件種別
    public const KIND_EXCLUSIVE_AREA = 3;           // 専有面積
    public const KIND_LOFT_AREA = 4;                // ロフト面積
    public const KIND_BATH_TOILET = 5;              // BT別
    public const KIND_RENT = 6;                     // 賃料
    public const KIND_KYOEKI = 7;                   // 共益費
    public const KIND_UNIT_PRICE = 8;               // 賃料＋共益費合計平米単価
    public const KIND_RENEWAL_FEE = 9;              // 更新料
    public const KIND_REIKIN = 10;                  // 礼金
    public const KIND_SHIKIKIN = 11;                // 敷金
    public const KIND_CLEANING_FEE = 12;            // 退去時清掃費
    public const KIND_COLLECTED_CLEANING_FEE = 13;  // 退去時徴収清掃費
    public const KIND_FR_PERIOD = 14;                    // FR期間
    public const KIND_FR_CONTRACT = 15;                  // FR違約
    public const KIND_AD_FEE = 16;                       // 募集広告料
    public const KIND_COMMISSION_FEE = 17;               // 募集委託料
    public const KIND_PETIT = 18;                        // プチ
    public const KIND_EQUIPMENT = 19;                    // 設備
    public const KIND_FREE_INPUT = 20;                   // 手入力

    protected $table = 'suggest_empty_room_new_equipments';

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
        ];
    }

    public static function createEquipments($suggestEmptyRoomId, $investmentId, $investmentRoomId) {
        $investmentRoom = InvestmentRoom::where('investment_id', $investmentId)
            ->where('investment_room_id', $investmentRoomId)
            ->first();

        $newEquipments = [];

        $newEquipments[] = [   // 契約携帯
            'suggest_empty_room_id' => $suggestEmptyRoomId,
            'kind' => self::KIND_CONTRACT,
            'order_num' => 1,
        ];

        $newEquipments[] = [   // 物件種別
            'suggest_empty_room_id' => $suggestEmptyRoomId,
            'kind' => self::KIND_INVESTMENT_TYPE,
            'order_num' => 2,
        ];

        $newEquipments[] = [   // 専有面積
            'suggest_empty_room_id' => $suggestEmptyRoomId,
            'kind' => self::KIND_EXCLUSIVE_AREA,
            'order_num' => 3,
            'old_condition' => $investmentRoom->area_size ?? null,
        ];

        $newEquipments[] = [   // ロフト面積
            'suggest_empty_room_id' => $suggestEmptyRoomId,
            'kind' => self::KIND_LOFT_AREA,
            'order_num' => 4,
        ];

        $newEquipments[] = [   // BT別
            'suggest_empty_room_id' => $suggestEmptyRoomId,
            'kind' => self::KIND_BATH_TOILET,
            'order_num' => 5,
        ];

        $newEquipments[] = [   // 賃料
            'suggest_empty_room_id' => $suggestEmptyRoomId,
            'kind' => self::KIND_RENT,
            'order_num' => 6,
            'old_condition' => $investmentRoom->money ?? null,
        ];

        $newEquipments[] = [   // 共益費
            'suggest_empty_room_id' => $suggestEmptyRoomId,
            'kind' => self::KIND_KYOEKI,
            'order_num' => 7,
            'old_condition' => $investmentRoom->kyoeki ?? null,
        ];

        // 賃料＋共益費合計平米単価
        $old_condition = null;
        if ($investmentRoom && $investmentRoom->area_size) {
            $old_condition = round(($investmentRoom->money + $investmentRoom->kyoeki) / $investmentRoom->area_size, 2);
        }
        $newEquipments[] = [
            'suggest_empty_room_id' => $suggestEmptyRoomId,
            'kind' => self::KIND_UNIT_PRICE,
            'order_num' => 8,
            'old_condition' => $old_condition,
        ];

        $newEquipments[] = [   // 更新料
            'suggest_empty_room_id' => $suggestEmptyRoomId,
            'kind' => self::KIND_RENEWAL_FEE,
            'order_num' => 9,
        ];

        $newEquipments[] = [   // 礼金
            'suggest_empty_room_id' => $suggestEmptyRoomId,
            'kind' => self::KIND_REIKIN,
            'order_num' => 10,
            'old_condition' => $investmentRoom ? ($investmentRoom->reikin ? $investmentRoom->reikin * 100 : null) : null,
        ];

        $newEquipments[] = [   // 敷金
            'suggest_empty_room_id' => $suggestEmptyRoomId,
            'kind' => self::KIND_SHIKIKIN,
            'order_num' => 11,
            'old_condition' => $investmentRoom ? ($investmentRoom->sikikin ? $investmentRoom->sikikin * 100 : null) : null,
        ];

        $newEquipments[] = [   // 退去時清掃費
            'suggest_empty_room_id' => $suggestEmptyRoomId,
            'kind' => self::KIND_CLEANING_FEE,
            'order_num' => 12,
        ];

        $newEquipments[] = [   // 退去時徴収清掃費
            'suggest_empty_room_id' => $suggestEmptyRoomId,
            'kind' => self::KIND_COLLECTED_CLEANING_FEE,
            'order_num' => 13,
        ];

        $newEquipments[] = [   // FR期間
            'suggest_empty_room_id' => $suggestEmptyRoomId,
            'kind' => self::KIND_FR_PERIOD,
            'order_num' => 14,
        ];

        $newEquipments[] = [   // FR違約
            'suggest_empty_room_id' => $suggestEmptyRoomId,
            'kind' => self::KIND_FR_CONTRACT,
            'order_num' => 15,
        ];

        $newEquipments[] = [   // 募集広告料
            'suggest_empty_room_id' => $suggestEmptyRoomId,
            'kind' => self::KIND_AD_FEE,
            'order_num' => 16,
        ];

        $newEquipments[] = [   // 募集委託料
            'suggest_empty_room_id' => $suggestEmptyRoomId,
            'kind' => self::KIND_COMMISSION_FEE,
            'order_num' => 17,
        ];

        // プチ
        $order_num = 18;
        for ($i = 0; $i < 7; $i++) {
            $newEquipments[] = [
                'suggest_empty_room_id' => $suggestEmptyRoomId,
                'kind' => self::KIND_PETIT,
                'order_num' => $order_num,
            ];
            $order_num++;
        }

        // 設備
        for ($i = 0; $i < 5; $i++) {
            $newEquipments[] = [
                'suggest_empty_room_id' => $suggestEmptyRoomId,
                'kind' => self::KIND_EQUIPMENT,
                'order_num' => $order_num,
            ];
            $order_num++;
        }

        // 手入力
        for ($i = 0; $i < 3; $i++) {
            $newEquipments[] = [
                'suggest_empty_room_id' => $suggestEmptyRoomId,
                'kind' => self::KIND_FREE_INPUT,
                'order_num' => $order_num,
            ];
            $order_num++;
        }

        foreach ($newEquipments as $newEquipment) {
            self::create($newEquipment);
        }
    }

}
