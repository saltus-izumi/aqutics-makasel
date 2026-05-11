<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('city_ranks', function (Blueprint $table) {
            $table->id();
            $table->string('item_name')->nullable()->comment('名称');
            $table->string('disp_rank')->nullable()->comment('並び順');

            $table->integer('created_user_id')->nullable()->comment('データ登録スタッフID');
            $table->dateTime('user_created_at')->nullable()->comment('データ登録日時（ スタッフ）');
            $table->integer('updated_user_id')->nullable()->comment('データ更新スタッフID');
            $table->dateTime('user_updated_at')->nullable()->comment('データ更新日時（スタッフ）');
            $table->integer('deleted_user_id')->nullable()->comment('データ削除スタッフID');
            $table->dateTime('user_deleted_at')->nullable()->comment('データ削除日時（スタッフ）');
            $table->dateTime('created_at')->nullable()->comment('データ登録日時');
            $table->dateTime('updated_at')->nullable()->comment('データ更新日時');
            $table->dateTime('deleted_at')->nullable()->comment('データ削除日時');
        });

        DB::table('city_ranks')->insert([
            ['id' => 1, 'item_name' => 'S', 'disp_rank' => 1],
            ['id' => 2, 'item_name' => 'A', 'disp_rank' => 2],
            ['id' => 3, 'item_name' => 'B', 'disp_rank' => 3],
            ['id' => 4, 'item_name' => 'C', 'disp_rank' => 4],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('city_ranks');
    }
};
