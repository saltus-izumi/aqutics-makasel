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
        Schema::create('ge_progress_files', function (Blueprint $table) {
            $table->id();
            $table->integer('ge_progress_id')->comment('原復プロセスID');
            $table->integer('file_kind')->comment('ファイル種別');
            $table->string('file_name', 255)->nullable()->comment('ファイル名');
            $table->string('file_path', 255)->nullable()->comment('ファイルパス');
            $table->datetime('upload_at')->nullable()->comment('アップロード日時');
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
        Schema::dropIfExists('ge_progress_files');
    }
};
