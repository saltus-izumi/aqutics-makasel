<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnSite extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    const ONSITE_REQEST_KIND_TRUSH   = 1;                   // ゴミ
    const ONSITE_REQEST_KIND_NOISE   = 2;                   // 騒音
    const ONSITE_REQEST_KIND_BICYCLE = 3;                   // 自転車
    const ONSITE_REQEST_KIND_BIKE    = 4;                   // バイク
    const ONSITE_REQEST_KIND_OVERDUE = 5;                   // 滞納訪問
    const ONSITE_REQEST_KIND_BULLETIN_BOARD = 6;            // 掲示板
    const ONSITE_REQEST_KIND_ORIGINAL_SUBTRACTION = 401;    // 原復引算
    const ONSITE_REQEST_KIND_COMPLETION_SHOOTING = 402;     // 完工撮影
    const ONSITE_REQEST_KIND_OTHERS  = 500;                 // その他依頼

    protected $guarded = [
        'id'
    ];

    /**
     * Onsite登録
     */
    public static function regit($inventmentRoom, $requestKinds, $leavingDate ,$now, $loginUser, $progressId = null)
    {
        $mapAddDays = [
            // TODO: 空室提案に対して足りないので確認の必要あり
            //Onsite::ONSITE_REQEST_KIND_ORIGINAL_RESTORATION => 0,         // 原復立会',
            Onsite::ONSITE_REQEST_KIND_ORIGINAL_SUBTRACTION => 4,           // 原復引算',
            Onsite::ONSITE_REQEST_KIND_COMPLETION_SHOOTING  => 21,          // 完工撮影',
        ];

        if( $inventmentRoom->leaving_date == null){
            $data = [];
            // 退去日が現れた場合は登録
            // 新規
            foreach( $requestKinds as $requestId ){
                $data[] = [   //
                        'investment_id'     => $inventmentRoom->investment_id,      // 物件id
                        'investment_room_id'=> $inventmentRoom->investment_room_id,	// 物件部屋ID
                        'request_date'      => $now,                                // 依頼日時
                        'request_kind_id'   => $requestId,                         // 依頼種別ID
                        'due_date'          => $leavingDate->addDay(),
                                                                                    // 期限日時
                        'responsible_id'  => $loginUser['id'],	                    // 担当者ID
                        'investment_empty_room_id' => $inventmentRoom->id,	        // 空き部屋情報ID
                ];
                if(!is_null($progressId)){
                    $data['progress_id'] = $progressId;
                }
            }
            $entities = $this->newEntities($data);
            if( $this->saveMany($entities) == false ){
                // エラーの場合、どうする？
            }
        }else if(strcmp($leavingDate->i18nFormat('yyyy-MM-dd'), $emptyRoom['leaving_date']->i18nFormat('yyyy-MM-dd')) != 0 ){
            // 退去日が変更の場合、該当ONSITEがある場合は更新、
            // 更新処理
            $onsites = $this->find('all',[
                'conditions' => ['investment_empty_room_id' => $emptyRoom['id']]
            ])->all();

            foreach($onsites as $onsite){
                $request_id = $onsite['request_kind_id'];
                $data = [
                    'id' => $onsite['id'],
                ];
                if(!is_null($progressId)){
                    $data['progress_id'] = $progressId;
                }
                if( isset( $mapAddDays[$request_id] ) == true ){
                    // 日付変更
                    $data['due_date'] = $leavingDate->addDays($mapAddDays[$request_id]);
                }
                $entity = $this->patchEntity($onsite, $data);
                if( $this->save($entity) == false ){
                    // エラーの場合、どうする？
                }
            }

        }

    }

}
