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
        if (Schema::hasTable('en_progress_files')) {
            $backupTable = 'en_progress_files_' . date('YmdHis');
            if (Schema::hasTable($backupTable)) {
                throw new RuntimeException("{$backupTable} table already exists.");
            }

            Schema::rename('en_progress_files', $backupTable);
        }

        Schema::create('en_progress_files', function (Blueprint $table) {
            $table->increments('id')->comment('ENプロセスファイルID');
            $table->integer('en_progress_id')->nullable()->comment('EN進捗ID');
            $table->integer('file_kind')->comment('ファイル種別');
            $table->string('file_url')->nullable()->comment('ファイルURL');

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
        Schema::dropIfExists('en_progress_files');
    }
};
