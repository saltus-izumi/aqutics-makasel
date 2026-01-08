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
        Schema::create('operations', function (Blueprint $table) {
            $table->id()->comment('オペレーションID');
            $table->integer('thread_id')->comment('スレッドID');
            $table->integer('thread_message_id')->comment('メッセージID');
            $table->integer('operation_template_id')->comment('オペレーションテンプレートID');
            $table->integer('assigned_user_id')->nullable()->comment('担当ユーザID');
            $table->integer('owner_id')->comment('オーナーID');
            $table->integer('investment_id')->comment('物件ID');
            $table->integer('investment_room_id')->comment('物件部屋ID');
            $table->integer('te_progress_id')->nullable()->comment('TEプロセスID');
            $table->integer('status')->default(1)->comment('ステータス');
            $table->integer('owner_message_id')->nullable()->comment('オーナーメッセージID');
            $table->datetime('sent_at')->nullable()->comment('送信日時');
            $table->datetime('read_at')->nullable()->comment('既読日時');
            $table->datetime('replied_at')->nullable()->comment('返信日時');
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
        Schema::dropIfExists('operations');
    }
};
