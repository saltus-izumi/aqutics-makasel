<?php

use App\Models\TeProgress;
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
        Schema::table('te_progresses', function (Blueprint $table) {
            $table->integer('next_action')->nullable()->after('contractor_no')->comment('ネクストアクション');
            $table->integer('executor_user_id')->nullable()->after('responsible_id')->comment('実行者');
            $table->integer('trading_company_2_id')->nullable()->after('genpuku_gyousha_id')->comment('指定業者2');
            $table->integer('trading_company_3_id')->nullable()->after('trading_company_2_id')->comment('指定業者3');
            $table->integer('nyuuden_date_state')->default(0)->after('nyuuden_date')->comment('入電日ステータス');
            $table->integer('gencho_date_state')->default(0)->after('gencho_date')->comment('現調日ステータス');
            $table->date('cost_received_date')->nullable()->after('mitsumori_date')->comment('下代受信日');
            $table->integer('cost_received_date_state')->default(0)->after('cost_received_date')->comment('下代受信日ステータス');
            $table->integer('cost_amount')->default(0)->after('cost_received_date_state')->comment('下代');
            $table->date('charge_received_date')->nullable()->after('cost_amount')->comment('上代受信日');
            $table->integer('charge_received_date_state')->default(0)->after('charge_received_date')->comment('上代受信日ステータス');
            $table->integer('charge_amount')->default(0)->after('charge_received_date_state')->comment('上代');
            $table->integer('own_suggestion_date_state')->default(0)->after('own_suggestion_date')->comment('OWN修繕提案ステータス');
            $table->integer('own_consent_date_state')->default(0)->after('own_consent_date')->comment('OWN修繕承諾ステータス');
            $table->integer('pc_hachu_date_state')->default(0)->after('pc_hachu_date')->comment('PC修繕発注ステータス');
            $table->integer('pc_kanko_receive_date_state')->default(0)->after('pc_kanko_receive_date')->comment('PC完工受信ステータス');
            $table->integer('pc_kanko_report_date_state')->default(0)->after('pc_kanko_report_date')->comment('PC完工報告ステータス');
            $table->integer('kakumei_koujo_date_state')->default(0)->after('kakumei_koujo_date')->comment('革命控除ステータス');
            $table->integer('kanko_yotei_date_state')->default(0)->after('kanko_yotei_date')->comment('完工予定日ステータス');
            $table->integer('complete_date_state')->default(0)->after('complete_date')->comment('完了日ステータス');
            $table->text('executor_to_responsible_message')->nullable()->after('kakumei_memo')->comment('実行担当 ⇒ 責任担当');
        });

        DB::transaction(function() {
            TeProgress::query()
                ->orderBy('id')
                ->chunkById(500, function ($teProgresses) {
                    foreach ($teProgresses as $teProgress) {
                        $teProgress->cost_received_date = $teProgress->mitsumori_date;
                        $teProgress->charge_received_date = $teProgress->mitsumori_created_date;
                        $teProgress->nyuuden_date_state = $teProgress->nyuuden_date ? 1 : 0;
                        $teProgress->gencho_date_state = $teProgress->gencho_date ? 1 : 0;
                        $teProgress->cost_received_date_state = $teProgress->cost_received_date ? 1 : 0;
                        $teProgress->charge_received_date_state = $teProgress->charge_received_date ? 1 : 0;
                        $teProgress->own_suggestion_date_state = $teProgress->own_suggestion_date ? 1 : 0;
                        $teProgress->own_consent_date_state = $teProgress->own_consent_date ? 1 : 0;
                        $teProgress->pc_hachu_date_state = $teProgress->pc_hachu_date ? 1 : 0;
                        $teProgress->pc_kanko_receive_date_state = $teProgress->pc_kanko_receive_date ? 1 : 0;
                        $teProgress->pc_kanko_report_date_state = $teProgress->pc_kanko_report_date ? 1 : 0;
                        $teProgress->kakumei_koujo_date_state = $teProgress->kakumei_koujo_date ? 1 : 0;
                        $teProgress->kanko_yotei_date_state = $teProgress->kanko_yotei_date ? 1 : 0;
                        $teProgress->complete_date_state = $teProgress->complete_date ? 1 : 0;

                        $teProgress->resetNextAction();

                        $teProgress->save();
                    }
                });
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('te_progresses', function (Blueprint $table) {
            $table->dropColumn([
                'next_action',
                'executor_user_id',
                'trading_company_2_id',
                'trading_company_3_id',
                'nyuuden_date_state',
                'gencho_date_state',
                'cost_received_date',
                'cost_received_date_state',
                'cost_amount',
                'charge_received_date',
                'charge_received_date_state',
                'charge_amount',
                'own_suggestion_date_state',
                'own_consent_date_state',
                'pc_hachu_date_state',
                'pc_kanko_receive_date_state',
                'pc_kanko_report_date_state',
                'kakumei_koujo_date_state',
                'kanko_yotei_date_state',
                'complete_date_state',
                'executor_to_responsible_message',
            ]);
        });
    }
};
