<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class insert_equipment_category1_masters extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('equipment_category1_masters')->insert([
            ['item_name' => '給排水設備', 'disp_rank' => 1],
            ['item_name' => '浄化槽設備', 'disp_rank' => 2],
            ['item_name' => '電気設備', 'disp_rank' => 3],
            ['item_name' => '電気温水器', 'disp_rank' => 4],
            ['item_name' => 'ガス設備', 'disp_rank' => 5],
            ['item_name' => '共聴アンテナ', 'disp_rank' => 6],
            ['item_name' => '共用灯', 'disp_rank' => 7],
            ['item_name' => '昇降機設備', 'disp_rank' => 8],
            ['item_name' => '機械式駐車場', 'disp_rank' => 9],
            ['item_name' => '散水栓', 'disp_rank' => 10],
            ['item_name' => 'ゴミ置き場', 'disp_rank' => 11],
        ]);
    }
}
