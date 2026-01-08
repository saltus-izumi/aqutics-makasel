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
        Schema::create('threads', function (Blueprint $table) {
            $table->id()->comment('スレッドID');
            $table->integer('thread_type')->comment('スレッド種別');
            $table->integer('owner_id')->comment('オーナーID');
            $table->integer('investment_id')->comment('物件ID');
            $table->integer('investment_room_id')->comment('物件部屋ID');
            $table->datetime('first_post_at')->nullable()->comment('初回投稿日時');
            $table->datetime('last_post_at')->nullable()->comment('最終投稿日時');
            $table->integer('status')->default(1)->comment('ステータス');
            $table->integer('created_user_id')->nullable()->comment('データ登録スタッフID');
            $table->datetime('user_created_at')->nullable()->comment('データ登録日時（スタッフ）');
            $table->integer('updated_user_id')->nullable()->comment('データ更新スタッフID');
            $table->datetime('user_updated_at')->nullable()->comment('データ更新日時（スタッフ）');
            $table->integer('deleted_user_id')->nullable()->comment('データ削除スタッフID');
            $table->datetime('user_deleted_at')->nullable()->comment('データ削除日時（スタッフ）');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('threads');
    }
};
