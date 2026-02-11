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
        Schema::table('ge_progresses', function (Blueprint $table) {
            $table->text('restoration_company_message')->nullable()->comment('実行担当 ⇒ 原復会社')->after('estimate_note_message');
            $table->text('memo')->nullable()->comment('メモ')->after('restoration_company_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ge_progresses', function (Blueprint $table) {
            $table->dropColumn('restoration_company_message');
            $table->dropColumn('memo');
        });
    }
};
