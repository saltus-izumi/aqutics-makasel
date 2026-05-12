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
        Schema::table('investments', function (Blueprint $table) {
            $table->integer('city_rank_id')->nullable()->comment('都市格')->after('is_management_active');
            $table->string('structure_floors')->nullable()->comment('構造（階数）')->after('city_rank_id');
            $table->integer('management_plan_id')->nullable()->comment('管理プラン')->after('structure_floors');
            $table->integer('management_fee_rate')->nullable()->comment('管理料')->after('management_plan_id');
            $table->integer('recruitment_fee_rate')->nullable()->comment('募集料')->after('management_fee_rate');
            $table->integer('renewal_fee_rate')->nullable()->comment('更新料')->after('recruitment_fee_rate');
            $table->integer('emergency_amount')->nullable()->comment('緊急')->after('renewal_fee_rate');
            $table->integer('system_amount')->nullable()->comment('システム')->after('emergency_amount');
            $table->integer('cleaning_plan_id')->nullable()->comment('清掃プラン')->after('system_amount');
            $table->integer('cleaning_fee_amount')->nullable()->comment('清掃料')->after('cleaning_plan_id');
            $table->integer('garbage_option_amount')->nullable()->comment('ゴミオプション')->after('cleaning_fee_amount');
            $table->integer('building_maintenance_plan_id')->nullable()->comment('建物保守プラン')->after('garbage_option_amount');
            $table->integer('building_maintenance_fee_amount')->nullable()->comment('保守料金')->after('building_maintenance_plan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->dropColumn([
                'city_rank_id',
                'structure_floors',
                'management_plan_id',
                'management_fee_rate',
                'recruitment_fee_rate',
                'renewal_fee_rate',
                'emergency_amount',
                'system_amount',
                'cleaning_plan_id',
                'cleaning_fee_amount',
                'garbage_option_amount',
                'building_maintenance_plan_id',
                'building_maintenance_fee_amount',
            ]);
        });
    }
};
