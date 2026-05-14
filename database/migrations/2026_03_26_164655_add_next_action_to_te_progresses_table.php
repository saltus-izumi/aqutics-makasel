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
            if (!Schema::hasColumn('te_progresses', 'operation_thread_id')) {
                $table->integer('operation_thread_id')->nullable()->comment('オペレーションスレッドID');
            }
            if (!Schema::hasColumn('te_progresses', 'operation_id')) {
                $table->integer('operation_id')->nullable()->comment('オペレーションID');
            }
            if (!Schema::hasColumn('te_progresses', 'next_action')) {
                $table->integer('next_action')->nullable()->comment('ネクストアクション');
            }
            if (!Schema::hasColumn('te_progresses', 'executor_user_id')) {
                $table->integer('executor_user_id')->nullable()->comment('実行者');
            }
            if (!Schema::hasColumn('te_progresses', 'trading_company_2_id')) {
                $table->integer('trading_company_2_id')->nullable()->comment('指定業者2');
            }
            if (!Schema::hasColumn('te_progresses', 'trading_company_3_id')) {
                $table->integer('trading_company_3_id')->nullable()->comment('指定業者3');
            }
            if (!Schema::hasColumn('te_progresses', 'nyuuden_date_state')) {
                $table->integer('nyuuden_date_state')->default(0)->comment('入電日ステータス');
            }
            if (!Schema::hasColumn('te_progresses', 'gencho_date_state')) {
                $table->integer('gencho_date_state')->default(0)->comment('現調日ステータス');
            }
            if (!Schema::hasColumn('te_progresses', 'cost_received_date')) {
                $table->date('cost_received_date')->nullable()->comment('下代受信日');
            }
            if (!Schema::hasColumn('te_progresses', 'cost_received_date_state')) {
                $table->integer('cost_received_date_state')->default(0)->comment('下代受信日ステータス');
            }
            if (!Schema::hasColumn('te_progresses', 'cost_amount')) {
                $table->integer('cost_amount')->default(0)->comment('下代');
            }
            if (!Schema::hasColumn('te_progresses', 'charge_received_date')) {
                $table->date('charge_received_date')->nullable()->comment('上代受信日');
            }
            if (!Schema::hasColumn('te_progresses', 'charge_received_date_state')) {
                $table->integer('charge_received_date_state')->default(0)->comment('上代受信日ステータス');
            }
            if (!Schema::hasColumn('te_progresses', 'charge_amount')) {
                $table->integer('charge_amount')->default(0)->comment('上代');
            }
            if (!Schema::hasColumn('te_progresses', 'own_suggestion_date_state')) {
                $table->integer('own_suggestion_date_state')->default(0)->comment('OWN修繕提案ステータス');
            }
            if (!Schema::hasColumn('te_progresses', 'own_consent_date_state')) {
                $table->integer('own_consent_date_state')->default(0)->comment('OWN修繕承諾ステータス');
            }
            if (!Schema::hasColumn('te_progresses', 'pc_hachu_date_state')) {
                $table->integer('pc_hachu_date_state')->default(0)->comment('PC修繕発注ステータス');
            }
            if (!Schema::hasColumn('te_progresses', 'pc_kanko_receive_date_state')) {
                $table->integer('pc_kanko_receive_date_state')->default(0)->comment('PC完工受信ステータス');
            }
            if (!Schema::hasColumn('te_progresses', 'pc_kanko_report_date_state')) {
                $table->integer('pc_kanko_report_date_state')->default(0)->comment('PC完工報告ステータス');
            }
            if (!Schema::hasColumn('te_progresses', 'kakumei_koujo_date_state')) {
                $table->integer('kakumei_koujo_date_state')->default(0)->comment('革命控除ステータス');
            }
            if (!Schema::hasColumn('te_progresses', 'kanko_yotei_date_state')) {
                $table->integer('kanko_yotei_date_state')->default(0)->comment('完工予定日ステータス');
            }
            if (!Schema::hasColumn('te_progresses', 'complete_date_state')) {
                $table->integer('complete_date_state')->default(0)->comment('完了日ステータス');
            }
            if (!Schema::hasColumn('te_progresses', 'executor_to_responsible_message')) {
                $table->text('executor_to_responsible_message')->nullable()->comment('実行担当 ⇒ 責任担当');
            }
            if (!Schema::hasColumn('te_progresses', 'is_proper_work_burden')) {
                $table->integer('is_proper_work_burden')->nullable()->comment('適正工事（負担）');
            }
            if (!Schema::hasColumn('te_progresses', 'is_proper_price')) {
                $table->integer('is_proper_price')->nullable()->comment('適正価格');
            }
            if (!Schema::hasColumn('te_progresses', 'correction_instruction_message')) {
                $table->text('correction_instruction_message')->nullable()->comment('実行担当へ修正指示');
            }
            if (!Schema::hasColumn('te_progresses', 'estimate_note_message')) {
                $table->text('estimate_note_message')->nullable()->comment('見積書備考入力内容');
            }
            if (!Schema::hasColumn('te_progresses', 'owner_proposal_operation_id')) {
                $table->integer('owner_proposal_operation_id')->nullable()->comment('オーナー提案オペレーションID');
            }
            if (!Schema::hasColumn('te_progresses', 'completion_report_operation_id')) {
                $table->integer('completion_report_operation_id')->nullable()->comment('完了報告オペレーションID');
            }
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
                'is_proper_work_burden',
                'is_proper_price',
                'correction_instruction_message',
                'estimate_note_message',
                'owner_proposal_operation_id',
                'completion_report_operation_id',
            ]);
        });
    }
};
