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
        Schema::table('users', function (Blueprint $table) {
            // created_at と updated_at を追加（既存データがあるためnullable）
            $table->timestamps();

            // ソフトデリート用の deleted_at を追加
            $table->softDeletes();
        });

        // 既存の削除済みデータには deleted_at を補完
        DB::table('users')
            ->where('deleted', 1)
            ->update(['deleted_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropTimestamps();
            $table->dropSoftDeletes();
        });
    }
};
