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
        Schema::table('progresses', function (Blueprint $table) {
            $table->unsignedTinyInteger('taikyo_date_state')->default(0)->comment('退去日ステータス')->after('taikyo_date');
            $table->unsignedTinyInteger('tsuden_state')->default(0)->comment('通電ステータス')->after('tsuden');
            $table->unsignedTinyInteger('tenant_charge_confirmed_date_state')->default(0)->comment('借主負担確定ステータス')->after('tenant_charge_confirmed_date');
            $table->unsignedTinyInteger('genpuku_teian_date_state')->default(0)->comment('OWN原復提案日ステータス')->after('genpuku_teian_date');
            $table->unsignedTinyInteger('genpuku_teian_kyodaku_date_state')->default(0)->comment('OWN原復承諾日ステータス')->after('genpuku_teian_kyodaku_date');
            $table->unsignedTinyInteger('genpuku_kouji_hachu_date_state')->default(0)->comment('原復発注日ステータス')->after('genpuku_kouji_hachu_date');
            $table->unsignedTinyInteger('kanko_yotei_date_state')->default(0)->comment('完工予定日ステータス')->after('kanko_yotei_date');
            $table->unsignedTinyInteger('kanko_jyushin_date_state')->default(0)->comment('完工受信日ステータス')->after('kanko_jyushin_date');
            $table->unsignedTinyInteger('owner_kanko_houkoku_date_state')->default(0)->comment('OWN完工報告日ステータス')->after('owner_kanko_houkoku_date');
            $table->unsignedTinyInteger('kakumei_koujo_touroku_date_state')->default(0)->comment('革命控除登録日ステータス')->after('kakumei_koujo_touroku_date');
            $table->unsignedTinyInteger('ge_complete_date_state')->default(0)->comment('原復完了日ステータス')->after('ge_complete_date');
        });

        DB::table('progresses')
            ->whereNotNull('taikyo_date')
            ->update(['taikyo_date_state' => 1]);

        DB::table('progresses')
            ->whereNotNull('tsuden')
            ->update(['tsuden_state' => 1]);

        DB::table('progresses')
            ->whereNotNull('tenant_charge_confirmed_date')
            ->update(['tenant_charge_confirmed_date_state' => 1]);

        DB::table('progresses')
            ->whereNotNull('genpuku_teian_date')
            ->update(['genpuku_teian_date_state' => 1]);

        DB::table('progresses')
            ->whereNotNull('genpuku_teian_kyodaku_date')
            ->update(['genpuku_teian_kyodaku_date_state' => 1]);

        DB::table('progresses')
            ->whereNotNull('genpuku_kouji_hachu_date')
            ->update(['genpuku_kouji_hachu_date_state' => 1]);

        DB::table('progresses')
            ->whereNotNull('kanko_yotei_date')
            ->update(['kanko_yotei_date_state' => 1]);

        DB::table('progresses')
            ->whereNotNull('kanko_jyushin_date')
            ->update(['kanko_jyushin_date_state' => 1]);

        DB::table('progresses')
            ->whereNotNull('owner_kanko_houkoku_date')
            ->update(['owner_kanko_houkoku_date_state' => 1]);

        DB::table('progresses')
            ->whereNotNull('kakumei_koujo_touroku_date')
            ->update(['kakumei_koujo_touroku_date_state' => 1]);

        DB::table('progresses')
            ->whereNotNull('ge_complete_date')
            ->update(['ge_complete_date_state' => 1]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progresses', function (Blueprint $table) {
            $table->dropColumn('taikyo_date_state');
            $table->dropColumn('tsuden_state');
            $table->dropColumn('tenant_charge_confirmed_date_state');
            $table->dropColumn('genpuku_teian_date_state');
            $table->dropColumn('genpuku_teian_kyodaku_date_state');
            $table->dropColumn('genpuku_kouji_hachu_date_state');
            $table->dropColumn('kanko_yotei_date_state');
            $table->dropColumn('kanko_jyushin_date_state');
            $table->dropColumn('owner_kanko_houkoku_date_state');
            $table->dropColumn('kakumei_koujo_touroku_date_state');
            $table->dropColumn('ge_complete_date_state');
        });
    }
};
