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
        Schema::create('thread_messages', function (Blueprint $table) {
            $table->id()->comment('オペレーションスレッドID');
            $table->integer('thread_id')->comment('スレッドID');
            $table->integer('sender_type')->comment('送信者種別');
            $table->integer('sender_user_id')->comment('送信者ユーザID');
            $table->text('title')->nullable()->comment('タイトル');
            $table->text('body')->nullable()->comment('本文');
            $table->text('extended_message')->nullable()->comment('拡張メッセージ');
            $table->integer('status')->default(1)->comment('ステータス');
            $table->datetime('sent_at')->nullable()->comment('送信日時');
            $table->datetime('read_at')->nullable()->comment('既読日時');
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
        Schema::dropIfExists('thread_messages');
    }
};
