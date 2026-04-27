<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class initEquipmentCategoryMastersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            '給排水設備' => [
                '直結直圧方式',
                '直結増圧方式',
                '受水槽_①高置方式',
                '受水槽_②圧力タンク方式',
                '受水槽_③加圧給水方式',
            ],
            '浄化槽設備' => [
                '①嫌気ろ床接触ばっ気方式,',
                '②生物ろ過方式',
            ],
            '電気設備' => [
                '借柱方式(柱上変圧器方式',
                '集合住宅用変圧方式',
                '借室方式',
                '借棟方式',
                'キュービクル方式',
            ],
            '電気温水器' => [
                '貯湯式（電気ヒーター）',
                'ヒートポンプ式（エコキュート）',
            ],
            'ガス設備' => [
                '都市ガス',
                'LPガス',
            ],
            '共聴アンテナ' => [
                'アンテナ',
                'CATV',
            ],
            '共用灯' => [
                'アナログタイムスイッチ（ダイヤル式）',
                'デジタルタイムスイッチ（週次/年次）',
                '天文タイマー（日の出入連動）',
                '時限スイッチ',
                '手動スイッチ',
            ],
            '昇降機設備' => [
                'ロープ式',
                '油圧式',
                'デジタルタイムスイッチ（週次/年次）',
                '天文タイマー（日の出入連動）',
                '時限スイッチ',
                '手動スイッチ',
            ],
            '機械式駐車場' => [
                '単純昇降ピット式',
                '昇降横行式',
                '昇降横行縦列式',
                '自走式',
                'タワー式',
                '地下循環式',
            ],
            '散水栓' => [
                '有り',
                '無し',
            ],
            'ゴミ置き場' => [
                '敷地内',
                '敷地外',
            ],
        ];

        DB::transaction(function () use ($categories) {
            DB::table('equipment_category2_masters')->delete();
            DB::table('equipment_category1_masters')->delete();

            $now = now();
            $category1Rows = [];
            $category1DispRank = 1;

            foreach (array_keys($categories) as $category1Name) {
                $category1Rows[] = [
                    'item_name' => $category1Name,
                    'disp_rank' => $category1DispRank,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $category1DispRank++;
            }

            DB::table('equipment_category1_masters')->insert($category1Rows);

            $category1IdMap = DB::table('equipment_category1_masters')
                ->pluck('id', 'item_name')
                ->toArray();

            $category2Rows = [];
            foreach ($categories as $category1Name => $children) {
                $category1Id = $category1IdMap[$category1Name] ?? null;
                if (!$category1Id) {
                    continue;
                }

                $category2DispRank = 1;
                foreach ($children as $childName) {
                    $category2Rows[] = [
                        'equipment_category1_master_id' => $category1Id,
                        'item_name' => $childName,
                        'disp_rank' => $category2DispRank,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    $category2DispRank++;
                }
            }

            if (!empty($category2Rows)) {
                DB::table('equipment_category2_masters')->insert($category2Rows);
            }
        });
    }
}
