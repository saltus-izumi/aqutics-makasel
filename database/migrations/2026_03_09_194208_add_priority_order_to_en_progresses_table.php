<?php

use App\Models\Progress;
use App\Models\EnProgress;
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
        Schema::table('en_progresses', function (Blueprint $table) {
            $table->integer('priority_order')->default(1)->after('progress_id')->comment('番手');

            $table->integer('responsible_user_id')->nullable()->after('reproposal_count')->comment('責任者');
            $table->integer('executor_user_id')->nullable()->after('responsible_user_id')->comment('実行者');

            $table->text('cancellation_reason')->nullable()->after('next_action')->comment('キャンセル理由');

            $table->integer('application_id')->nullable()->after('completed_date_state')->comment('申込ID');
            $table->string('contract_type')->nullable()->after('application_id')->comment('契約形態');
            $table->boolean('fr_active_flag')->default(false)->after('contract_type')->comment('FRアクティブ');
            $table->date('fr_start_date')->nullable()->after('fr_active_flag')->comment('FR期間（開始）');
            $table->date('fr_end_date')->nullable()->after('fr_start_date')->comment('FR期間（終了）');
            $table->date('desired_contract_date')->nullable()->after('fr_end_date')->comment('契約希望日');
            $table->date('planned_payment_date')->nullable()->after('desired_contract_date')->comment('入金予定日');
            $table->date('desired_move_in_date')->nullable()->after('planned_payment_date')->comment('入居希望日');
            $table->date('contract_start_date')->nullable()->after('desired_move_in_date')->comment('契約期間（開始）');
            $table->date('contract_end_date')->nullable()->after('contract_start_date')->comment('契約期間（終了）');
            $table->string('renewal_fee')->nullable()->after('contract_end_date')->comment('更新料');
            $table->string('guarantee_company_id')->nullable()->after('renewal_fee')->comment('保証会社');
            $table->string('guarantee_company_plan')->nullable()->after('guarantee_company_id')->comment('保証プラン名');
            $table->integer('guarantee_company_monthly_fee')->nullable()->after('guarantee_company_plan')->comment('保証月額費用');
            $table->integer('guarantee_company_status')->nullable()->after('guarantee_company_monthly_fee')->comment('保証ステータス');
            $table->string('fire_insurance_name')->nullable()->after('guarantee_company_status')->comment('火災保険名');
            $table->integer('fire_insurance_monthly_fee')->nullable()->after('fire_insurance_name')->comment('火災保険月額費用');
            $table->integer('fire_insurance_status')->nullable()->after('fire_insurance_monthly_fee')->comment('火災保険ステータス');
            $table->boolean('anshin_support_flag')->default(false)->after('fire_insurance_status')->comment('安心入居');
            $table->boolean('move_out_cleaning_flag')->default(false)->after('anshin_support_flag')->comment('退去時清掃徴収');
            $table->boolean('ac_cleaning_flag')->default(false)->after('move_out_cleaning_flag')->comment('ACクリーニング');
            $table->boolean('cancellation_penalty_flag')->default(false)->after('ac_cleaning_flag')->comment('解約違約金');
            $table->boolean('pet_allowed_flag')->default(false)->after('cancellation_penalty_flag')->comment('ペット');
            $table->boolean('instrument_allowed_flag')->default(false)->after('pet_allowed_flag')->comment('楽器');
            $table->boolean('fr_flag')->default(false)->after('instrument_allowed_flag')->comment('FR');
            $table->boolean('two_person_allowed_flag')->default(false)->after('fr_flag')->comment('二人入居');
            $table->integer('rent_fee')->nullable()->after('two_person_allowed_flag')->comment('賃料');
            $table->integer('common_service_fee')->nullable()->after('rent_fee')->comment('共益費');
            $table->integer('other_fixed_fee')->nullable()->after('common_service_fee')->comment('その他固定費');
            $table->integer('neighborhood_fee')->nullable()->after('other_fixed_fee')->comment('町内会費');
            $table->integer('parking_fee')->nullable()->after('neighborhood_fee')->comment('駐車場');
            $table->integer('water_fee')->nullable()->after('parking_fee')->comment('水道代');
            $table->integer('transfer_fee')->nullable()->after('water_fee')->comment('振替手数料');
            $table->integer('deposit_fee')->nullable()->after('transfer_fee')->comment('敷金');
            $table->integer('security_deposit_fee')->nullable()->after('deposit_fee')->comment('保証金');
            $table->integer('cleaning_fee')->nullable()->after('security_deposit_fee')->comment('退去時清掃費');
            $table->integer('key_money')->nullable()->after('cleaning_fee')->comment('礼金');
            $table->integer('key_antibacterial_fee')->nullable()->after('key_money')->comment('鍵・抗菌費');
            $table->integer('broker_company_id')->nullable()->after('key_antibacterial_fee')->comment('仲介会社ID');
            $table->text('memo')->nullable()->after('broker_company_id')->comment('メモ');
            $table->integer('guarantor_company_name')->nullable()->after('memo')->comment('保証会社名');
            $table->date('screening_application_date')->nullable()->after('guarantor_company_name')->comment('審査申込日');
            $table->integer('screening_result')->nullable()->after('screening_application_date')->comment('審査結果');
            $table->string('approval_number')->nullable()->after('screening_result')->comment('承認番号');
            $table->text('guarantor_plan')->nullable()->after('approval_number')->comment('保証プラン');
            $table->integer('guarantor_fee_burden')->nullable()->after('guarantor_plan')->comment('保証料負担');
            $table->text('condition_summary')->nullable()->after('guarantor_fee_burden')->comment('条件要約');
            $table->text('approval_notice_url')->nullable()->after('condition_summary')->comment('承認通知書URL');
            $table->boolean('identity_verification_flag')->default(false)->after('approval_notice_url')->comment('本人確認');
            $table->boolean('condition_match_flag')->default(false)->after('identity_verification_flag')->comment('条件整合');
            $table->boolean('antisocial_check_flag')->default(false)->after('condition_match_flag')->comment('反社確認');
            $table->boolean('special_agreement_note_flag')->default(false)->after('antisocial_check_flag')->comment('特約覚書');
            $table->integer('risk_category')->nullable()->after('special_agreement_note_flag')->comment('リスク区分');
            $table->boolean('escalation_flag')->default(false)->after('risk_category')->comment('エスカレーション');
            $table->integer('wp_approver_id')->nullable()->after('escalation_flag')->comment('WP承認者');
            $table->text('wp_screening_memo')->nullable()->after('wp_approver_id')->comment('WP審査メモ');
            $table->integer('approval_method')->nullable()->after('wp_screening_memo')->comment('承諾方式');
            $table->date('approval_acquired_date')->nullable()->after('approval_method')->comment('承諾取得日');
            $table->boolean('approval_condition')->default(false)->after('approval_acquired_date')->comment('承諾条件');
            $table->text('approval_condition_detail')->nullable()->after('approval_condition')->comment('承諾条件・条件がある場合');
            $table->text('owner_approval_memo')->nullable()->after('approval_condition_detail')->comment('OWN承諾メモ');
            $table->integer('total_payment_amount')->nullable()->after('owner_approval_memo')->comment('入金額合計');
            $table->date('invoice_due_date')->nullable()->after('total_payment_amount')->comment('請求期限');
            $table->integer('payment_status')->nullable()->after('invoice_due_date')->comment('入金状況');
            $table->text('payment_proof_url')->nullable()->after('payment_status')->comment('入金証跡URL');
            $table->integer('payment_confirmed_by')->nullable()->after('payment_proof_url')->comment('入金確認者');
            $table->text('initial_cost_memo')->nullable()->after('payment_confirmed_by')->comment('初期費用メモ');
        });

        DB::transaction(function() {
            Progress::query()
                ->with('investmentEmptyRoom')
                ->orderBy('id')
                ->chunkById(500, function ($progresses) {
                    foreach ($progresses as $progress) {
                        $enProgress = EnProgress::query()
                            ->where('progress_id', $progress->id)
                            ->first();
                        if ($enProgress) {
                            $enProgress->desired_contract_date = $progress->keiyaku_shiki_date;
                            $enProgress->responsible_user_id = $progress->en_responsible_id;
                            $enProgress->save();
                        }
                    }
                });
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('en_progresses', function (Blueprint $table) {
            $table->dropColumn([
                'priority_order',
                'responsible_user_id',
                'executor_user_id',
                'cancellation_reason',
                'application_id',
                'contract_type',
                'fr_active_flag',
                'fr_start_date',
                'fr_end_date',
                'desired_contract_date',
                'planned_payment_date',
                'desired_move_in_date',
                'contract_start_date',
                'contract_end_date',
                'renewal_fee',
                'guarantee_company_id',
                'guarantee_company_plan',
                'guarantee_company_monthly_fee',
                'guarantee_company_status',
                'fire_insurance_name',
                'fire_insurance_monthly_fee',
                'fire_insurance_status',
                'anshin_support_flag',
                'move_out_cleaning_flag',
                'ac_cleaning_flag',
                'cancellation_penalty_flag',
                'pet_allowed_flag',
                'instrument_allowed_flag',
                'fr_flag',
                'two_person_allowed_flag',
                'rent_fee',
                'common_service_fee',
                'other_fixed_fee',
                'neighborhood_fee',
                'parking_fee',
                'water_fee',
                'transfer_fee',
                'deposit_fee',
                'security_deposit_fee',
                'cleaning_fee',
                'key_money',
                'key_antibacterial_fee',
                'broker_company_id',
                'memo',
                'guarantor_company_name',
                'screening_application_date',
                'screening_result',
                'approval_number',
                'guarantor_plan',
                'guarantor_fee_burden',
                'condition_summary',
                'approval_notice_url',
                'identity_verification_flag',
                'condition_match_flag',
                'antisocial_check_flag',
                'special_agreement_note_flag',
                'risk_category',
                'escalation_flag',
                'wp_approver_id',
                'wp_screening_memo',
                'approval_method',
                'approval_acquired_date',
                'approval_condition',
                'approval_condition_detail',
                'owner_approval_memo',
                'total_payment_amount',
                'invoice_due_date',
                'payment_status',
                'payment_proof_url',
                'payment_confirmed_by',
                'initial_cost_memo',
            ]);
        });
    }
};
