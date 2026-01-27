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
        Schema::create('ge_progresses', function (Blueprint $table) {
            $table->id();

            $table->integer('progress_id')->comment('進捗ID');
            $table->integer('responsible_user_id')->nullable()->comment('責任者');
            $table->integer('executor_user_id')->nullable()->comment('実行者');
            $table->date('taikyo_accepted_date')->nullable()->comment('退去受付');
            $table->date('cancellation_date')->nullable()->comment('解約日');
            $table->date('taikyo_date')->nullable()->comment('退去日');
            $table->date('lower_estimate_date')->nullable()->comment('下代');
            $table->date('tsuuden_date')->nullable()->comment('通電');
            $table->date('tenant_charge_confirmed_date')->nullable()->comment('借主負担');
            $table->date('owner_proposed_date')->nullable()->comment('貸主提案');
            $table->date('owner_approved_date')->nullable()->comment('貸主承諾');
            $table->date('ordered_date')->nullable()->comment('発注');
            $table->date('completion_scheduled_date')->nullable()->comment('完工予定');
            $table->date('completion_received_date')->nullable()->comment('完工受信');
            $table->date('completion_reported_date')->nullable()->comment('完工報告');
            $table->date('kakumei_koujo_touroku_date')->nullable()->comment('革命控除');
            $table->date('completed_date')->nullable()->comment('完了');
            $table->integer('created_user_id')->nullable()->comment('データ登録スタッフID');
            $table->dateTime('user_created_at')->nullable()->comment('データ登録日時（ スタッフ）');
            $table->integer('updated_user_id')->nullable()->comment('データ更新スタッフID');
            $table->dateTime('user_updated_at')->nullable()->comment('データ更新日時（スタッフ）');
            $table->integer('deleted_user_id')->nullable()->comment('データ削除スタッフID');
            $table->dateTime('user_deleted_at')->nullable()->comment('データ削除日時（スタッフ）');

            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement(
            " INSERT INTO" .
            "   ge_progresses (" .
            "     progress_id," .                   // 進捗ID
            "     responsible_user_id," .           // 実行者
            "     taikyo_accepted_date," .          // 退去受付
            "     taikyo_date," .                   // 退去日
            // "     lower_estimate_date," .           // 下代
            "     tsuuden_date," .                  // 通電
            "     tenant_charge_confirmed_date," .  // 借主負担
            "     owner_proposed_date," .           // 貸主提案
            "     owner_approved_date," .           // 貸主承諾
            "     ordered_date," .                  // 発注
            "     completion_scheduled_date," .     // 完工予定
            "     completion_received_date," .      // 完工受信
            "     completion_reported_date," .      // 完工報告
            "     kakumei_koujo_touroku_date," .    // 革命控除
            "     completed_date," .                // 完了
            "     created_at," .
            "     updated_at)" .
            " SELECT" .
            "   id," .                              // 進捗ID
            "   genpuku_responsible_id," .          // 実行者
            "   taikyo_uketuke_date," .             // 退去受付
            "   taikyo_date," .                     // 退去日
            "   tsuden," .                          // 通電
            "   tenant_charge_confirmed_date," .    // 借主負担
            "   genpuku_teian_date," .              // 貸主提案
            "   genpuku_teian_kyodaku_date," .      // 貸主承諾
            "   genpuku_kouji_hachu_date," .        // 発注
            "   kanko_yotei_date," .                // 完工予定
            "   kanko_jyushin_date," .              // 完工受信
            "   owner_kanko_houkoku_date," .        // 完工報告
            "   kakumei_koujo_touroku_date," .      // 革命控除
            "   ge_complete_date," .                // 完了
            "   created," .
            "   modified" .
            " FROM" .
            "   progresses"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ge_progresses');
    }
};
