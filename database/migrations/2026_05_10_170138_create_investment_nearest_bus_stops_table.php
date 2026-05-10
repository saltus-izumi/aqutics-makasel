<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('investment_nearest_bus_stops', function (Blueprint $table) {
            $table->id();
            $table->integer('investment_id')->nullable()->comment('物件ID');
            $table->string('bus_stop_name')->nullable()->comment('バス停留所名');
            $table->integer('walking_minutes')->nullable()->comment('徒歩（分）');
            $table->string('nearest_line_name')->nullable()->comment('最寄り沿線名');
            $table->string('nearest_station_name')->nullable()->comment('最寄り駅');
            $table->integer('bus_minutes_to_station')->nullable()->comment('バス所要時間（バス停～駅）');
            
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_nearest_bus_stops');
    }
};
