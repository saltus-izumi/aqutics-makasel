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
        Schema::table('ge_progresses', function (Blueprint $table) {
            $table->integer('responsible_user_id')->nullable()->comment('責任者')->after('progress_id');

            $table->integer('trading_company_id')->nullable()->comment('原復業者ID')->after('executor_user_id');

            $table->date('move_out_received_date')->nullable()->comment('退去受付日')->after('next_action');
            $table->integer('move_out_received_date_state')->default(0)->comment('退去受付日ステータス')->after('move_out_received_date');

            $table->date('move_out_date')->nullable()->comment('退去日')->after('move_out_received_date_state');
            $table->integer('move_out_date_state')->default(0)->comment('退去日ステータス')->after('move_out_date');

            $table->date('cost_received_date')->nullable()->comment('解約日')->after('move_out_date_state');
            $table->integer('cost_received_date_state')->default(0)->comment('解約日ステータス')->after('cost_received_date');

            $table->date('power_activation_date')->nullable()->comment('通電日')->after('cost_received_date_state');
            $table->integer('power_activation_date_state')->default(0)->comment('通電日ステータス')->after('power_activation_date');

            $table->date('tenant_burden_confirmed_date')->nullable()->comment('借主負担確定日')->after('power_activation_date_state');
            $table->integer('tenant_burden_confirmed_date_state')->default(0)->comment('借主負担確定日ステータス')->after('tenant_burden_confirmed_date');

            $table->date('owner_proposed_date')->nullable()->comment('借貸主提案日')->after('tenant_burden_confirmed_date_state');
            $table->integer('owner_proposed_date_state')->default(0)->comment('借貸主提案日ステータス')->after('owner_proposed_date');

            $table->date('owner_approved_date')->nullable()->comment('貸主承諾日')->after('owner_proposed_date_state');
            $table->integer('owner_approved_date_state')->default(0)->comment('貸主承諾日ステータス')->after('owner_approved_date');

            $table->date('ordered_date')->nullable()->comment('発注日')->after('owner_approved_date_state');
            $table->integer('ordered_date_state')->default(0)->comment('発注日ステータス')->after('ordered_date');

            $table->date('completion_scheduled_date')->nullable()->comment('完工予定日')->after('ordered_date_state');
            $table->integer('completion_scheduled_date_state')->default(0)->comment('完工予定日ステータス')->after('completion_scheduled_date');

            $table->date('completion_received_date')->nullable()->comment('完工受信日')->after('completion_scheduled_date_state');
            $table->integer('completion_received_date_state')->default(0)->comment('完工受信日ステータス')->after('completion_received_date');

            $table->date('completion_reported_date')->nullable()->comment('完工報告日')->after('completion_received_date_state');
            $table->integer('completion_reported_date_state')->default(0)->comment('完工報告日ステータス')->after('completion_reported_date');

            $table->date('kakumei_registered_date')->nullable()->comment('革命控除登録日')->after('completion_reported_date_state');
            $table->integer('kakumei_registered_date_state')->default(0)->comment('革命控除登録日ステータス')->after('kakumei_registered_date');

            $table->date('completed_date')->nullable()->comment('完了日')->after('kakumei_registered_date_state');
            $table->integer('completed_date_state')->default(0)->comment('完了日ステータス')->after('completed_date');

            $table->date('cancellation_reversed_date')->nullable()->comment('解約キャンセル日')->after('completed_date_state');
            $table->integer('cancellation_reversed_date_state')->default(0)->comment('解約キャンセル日ステータス')->after('cancellation_reversed_date');

            $table->renameColumn('step1_confirmed', 'is_step1_confirmed');
            $table->renameColumn('responsible_person_message', 'executor_to_responsible_message');
            $table->renameColumn('restoration_company_message', 'executor_to_restoration_company_message');
        });

        DB::statement('
            UPDATE
                ge_progresses ge
            INNER JOIN
                progresses p ON p.id = ge.progress_id
            SET
                ge.responsible_user_id = p.genpuku_responsible_id,
                ge.trading_company_id = p.genpuku_gyousha_id,
                ge.move_out_received_date = p.taikyo_uketuke_date,
                ge.move_out_date = p.taikyo_date,
                ge.cost_received_date = p.genpuku_mitsumori_recieved_date,
                ge.power_activation_date = p.tsuden,
                ge.tenant_burden_confirmed_date = p.tenant_charge_confirmed_date,
                ge.owner_proposed_date = p.genpuku_teian_date,
                ge.owner_approved_date = p.genpuku_teian_kyodaku_date,
                ge.ordered_date = p.genpuku_kouji_hachu_date,
                ge.completion_scheduled_date = p.kanko_yotei_date,
                ge.completion_received_date = p.kanko_jyushin_date,
                ge.completion_reported_date = p.owner_kanko_houkoku_date,
                ge.kakumei_registered_date = p.kakumei_koujo_touroku_date,
                ge.completed_date = p.ge_complete_date,
                ge.cancellation_reversed_date = p.kaiyaku_cancellation_date
        ');

        DB::table('ge_progresses')
            ->whereNotNull('move_out_received_date')
            ->update(['move_out_received_date_state' => 1]);

        DB::table('ge_progresses')
            ->whereNotNull('move_out_date')
            ->update(['move_out_date_state' => 1]);

        DB::table('ge_progresses')
            ->whereNotNull('cost_received_date')
            ->update(['cost_received_date_state' => 1]);

        DB::table('ge_progresses')
            ->whereNotNull('power_activation_date')
            ->update(['power_activation_date_state' => 1]);

        DB::table('ge_progresses')
            ->whereNotNull('tenant_burden_confirmed_date')
            ->update(['tenant_burden_confirmed_date_state' => 1]);

        DB::table('ge_progresses')
            ->whereNotNull('owner_proposed_date')
            ->update(['owner_proposed_date_state' => 1]);

        DB::table('ge_progresses')
            ->whereNotNull('owner_approved_date')
            ->update(['owner_approved_date_state' => 1]);

        DB::table('ge_progresses')
            ->whereNotNull('ordered_date')
            ->update(['ordered_date_state' => 1]);

        DB::table('ge_progresses')
            ->whereNotNull('completion_scheduled_date')
            ->update(['completion_scheduled_date_state' => 1]);

        DB::table('ge_progresses')
            ->whereNotNull('completion_received_date')
            ->update(['completion_received_date_state' => 1]);

        DB::table('ge_progresses')
            ->whereNotNull('completion_reported_date')
            ->update(['completion_reported_date_state' => 1]);

        DB::table('ge_progresses')
            ->whereNotNull('kakumei_registered_date')
            ->update(['kakumei_registered_date_state' => 1]);

        DB::table('ge_progresses')
            ->whereNotNull('completed_date')
            ->update(['completed_date_state' => 1]);

        DB::table('ge_progresses')
            ->whereNotNull('cancellation_reversed_date')
            ->update(['cancellation_reversed_date_state' => 1]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ge_progresses', function (Blueprint $table) {
            $table->dropColumn('responsible_user_id');

            $table->dropColumn('trading_company_id');

            $table->dropColumn('move_out_received_date');
            $table->dropColumn('move_out_received_date_state');

            $table->dropColumn('move_out_date');
            $table->dropColumn('move_out_date_state');

            $table->dropColumn('cost_received_date');
            $table->dropColumn('cost_received_date_state');

            $table->dropColumn('power_activation_date');
            $table->dropColumn('power_activation_date_state');

            $table->dropColumn('tenant_burden_confirmed_date');
            $table->dropColumn('tenant_burden_confirmed_date_state');

            $table->dropColumn('owner_proposed_date');
            $table->dropColumn('owner_proposed_date_state');

            $table->dropColumn('owner_approved_date');
            $table->dropColumn('owner_approved_date_state');

            $table->dropColumn('ordered_date');
            $table->dropColumn('ordered_date_state');

            $table->dropColumn('completion_scheduled_date');
            $table->dropColumn('completion_scheduled_date_state');

            $table->dropColumn('completion_received_date');
            $table->dropColumn('completion_received_date_state');

            $table->dropColumn('completion_reported_date');
            $table->dropColumn('completion_reported_date_state');

            $table->dropColumn('kakumei_registered_date');
            $table->dropColumn('kakumei_registered_date_state');

            $table->dropColumn('completed_date');
            $table->dropColumn('completed_date_state');

            $table->dropColumn('cancellation_reversed_date');
            $table->dropColumn('cancellation_reversed_date_state');

            $table->renameColumn('is_step1_confirmed', 'step1_confirmed');
            $table->renameColumn('executor_to_responsible_message', 'responsible_person_message');
            $table->renameColumn('executor_to_restoration_company_message', 'restoration_company_message');
        });
    }
};
