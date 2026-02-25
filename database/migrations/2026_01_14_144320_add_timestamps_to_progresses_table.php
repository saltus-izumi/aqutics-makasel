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
        Schema::table('progresses', function (Blueprint $table) {
            $table->integer('created_user_id')->nullable()->comment('データ登録スタッフID')->after('keiri_bikou');
            $table->datetime('user_created_at')->nullable()->comment('データ登録日時（スタッフ）')->after('created_user_id');
            $table->integer('updated_user_id')->nullable()->comment('データ更新スタッフID')->after('user_created_at');
            $table->datetime('user_updated_at')->nullable()->comment('データ更新日時（スタッフ）')->after('updated_user_id');
            $table->integer('deleted_user_id')->nullable()->comment('データ削除スタッフID')->after('user_updated_at');
            $table->datetime('user_deleted_at')->nullable()->comment('データ削除日時（スタッフ）')->after('deleted_user_id');

            // created_at と updated_at を追加（既存データがあるためnullable）
            $table->timestamps();

            // ソフトデリート用の deleted_at を追加
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progresses', function (Blueprint $table) {
            $table->dropColumn('created_user_id');
            $table->dropColumn('user_created_at');
            $table->dropColumn('updated_user_id');
            $table->dropColumn('user_updated_at');
            $table->dropColumn('deleted_user_id');
            $table->dropColumn('user_deleted_at');

            $table->dropTimestamps();
            $table->dropSoftDeletes();
        });
    }
};
