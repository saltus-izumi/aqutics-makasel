<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OperationTemplate extends Model
{
    use SoftDeletes;

    public const OPERATION_GROUP_EN = 1;
    public const OPERATION_GROUP_LE = 2;
    public const OPERATION_GROUP_TE = 3;
    public const OPERATION_GROUP_PL = 4;
    public const OPERATION_GROUP_OTEHR = 5;

    public const OPERATION_GROUPS = [
        self::OPERATION_GROUP_EN => 'EN',
        self::OPERATION_GROUP_LE => 'LE',
        self::OPERATION_GROUP_TE => 'TE',
        self::OPERATION_GROUP_PL => '収支報告書',
        self::OPERATION_GROUP_OTEHR => 'その他',
    ];

    protected $guarded = [
        'id'
    ];

    public static function getGroupOptions()
    {
        $operationKinds = self::where('is_display', true)
            ->get();

        $options = [];
        foreach ($operationKinds as $operationKind) {
            $operation_group = self::OPERATION_GROUPS[$operationKind->operation_group_id];

            if (!array_key_exists($operation_group, $options)) {
                $options[$operation_group] = [];
            }

            $options[$operation_group][$operationKind->id] = $operationKind->value;
        }

        return $options;
    }

    /**
     * オーナーに紐づく物件のオプションを取得（Owner→Landlord→Investment）
     *
     * @param int|string $ownerId
     * @return array
     */
    public static function getOptionsByOperationGroupId($operationGroupId): array
    {
       $operationTemplates = self::where('operation_group_id', $operationGroupId)
            ->orderBy('id', 'asc')
            ->get();

        $options = [];
        if ($operationTemplates) {
            foreach ($operationTemplates as $operationTemplate) {
                $options[$operationTemplate->id] = $operationTemplate->operation_category;
            }
        }


        return $options;
    }
}
