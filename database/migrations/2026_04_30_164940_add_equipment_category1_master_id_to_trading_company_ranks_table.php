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
        Schema::table('trading_company_ranks', function (Blueprint $table) {
            $table->integer('equipment_category1_master_id')->nullable()->comment('設備カテゴリ1ID')->after('category3_master_id');
            $table->integer('equipment_category2_master_id')->nullable()->comment('設備カテゴリ2ID')->after('equipment_category1_master_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trading_company_ranks', function (Blueprint $table) {
            $table->dropColumn('equipment_category1_master_id');
            $table->dropColumn('equipment_category2_master_id');
        });
    }
};
