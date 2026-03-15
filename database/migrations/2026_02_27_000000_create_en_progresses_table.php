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
        Schema::create('en_progresses', function (Blueprint $table) {
            $table->increments('id')->comment('EN進捗ID');
            $table->integer('progress_id')->comment('進捗ID');
            $table->integer('reproposal_count')->default(0)->comment('再提案回数');

            $table->date('completion_scheduled_date')->nullable()->comment('完工予定日');
            $table->date('completion_date')->nullable()->comment('完工日');
            $table->date('start_date')->nullable()->comment('始期日');
            $table->integer('broker_id')->nullable()->comment('仲介会社ID');

            $table->integer('next_action')->nullable()->comment('ネクストアクション');

            $table->date('application_date')->nullable()->comment('申込日');
            $table->integer('application_date_state')->default(0)->comment('申込日ステータス');
            $table->date('guarantee_screening_date')->nullable()->comment('保証審査');
            $table->integer('guarantee_screening_date_state')->default(0)->comment('保証審査ステータス');
            $table->date('wp_screening_date')->nullable()->comment('WP審査');
            $table->integer('wp_screening_date_state')->default(0)->comment('WP審査ステータス');
            $table->date('owner_reported_date')->nullable()->comment('OWN報告');
            $table->integer('owner_reported_date_state')->default(0)->comment('OWN報告ステータス');
            $table->date('owner_approved_date')->nullable()->comment('OWN承諾');
            $table->integer('owner_approved_date_state')->default(0)->comment('OWN承諾ステータス');
            $table->date('start_date_confirmed_date')->nullable()->comment('始期日確定日');
            $table->integer('start_date_confirmed_date_state')->default(0)->comment('始期日確定日ステータス');
            $table->date('key_requested_date')->nullable()->comment('鍵依頼日');
            $table->integer('key_requested_date_state')->default(0)->comment('鍵依頼日ステータス');
            $table->date('invoice_issued_date')->nullable()->comment('請求発行');
            $table->integer('invoice_issued_date_state')->default(0)->comment('請求発行ステータス');
            $table->date('contract_sent_date')->nullable()->comment('契約発送');
            $table->integer('contract_sent_date_state')->default(0)->comment('契約発送ステータス');
            $table->date('contract_payment_date')->nullable()->comment('契約入金');
            $table->integer('contract_payment_date_state')->default(0)->comment('契約入金ステータス');
            $table->date('contract_collected_date')->nullable()->comment('契約回収');
            $table->integer('contract_collected_date_state')->default(0)->comment('契約回収ステータス');
            $table->date('electricity_cancellation_date')->nullable()->comment('電気解約');
            $table->integer('electricity_cancellation_date_state')->default(0)->comment('電気解約ステータス');
            $table->date('key_handover_date')->nullable()->comment('鍵渡し');
            $table->integer('key_handover_date_state')->default(0)->comment('鍵渡しステータス');
            $table->date('documents_archived_date')->nullable()->comment('書類格納');
            $table->integer('documents_archived_date_state')->default(0)->comment('書類格納ステータス');
            $table->date('completion_reported_date')->nullable()->comment('完了報告');
            $table->integer('completion_reported_date_state')->default(0)->comment('完了報告ステータス');
            $table->date('completed_date')->nullable()->comment('完了日');
            $table->integer('completed_date_state')->default(0)->comment('完了日ステータス');

            $table->integer('created_user_id')->nullable()->comment('データ登録スタッフID');
            $table->dateTime('user_created_at')->nullable()->comment('データ登録日時（ スタッフ）');
            $table->integer('updated_user_id')->nullable()->comment('データ更新スタッフID');
            $table->dateTime('user_updated_at')->nullable()->comment('データ更新日時（スタッフ）');
            $table->integer('deleted_user_id')->nullable()->comment('データ削除スタッフID');
            $table->dateTime('user_deleted_at')->nullable()->comment('データ削除日時（スタッフ）');

            $table->dateTime('created_at')->nullable()->comment('データ登録日時');
            $table->dateTime('updated_at')->nullable()->comment('データ更新日時');
            $table->dateTime('deleted_at')->nullable()->comment('データ削除日時');
        });

        DB::transaction(function() {
            Progress::query()
                ->with('investmentEmptyRoom')
                ->orderBy('id')
                ->chunkById(500, function ($progresses) {
                    foreach ($progresses as $progress) {
                        $enProgress = EnProgress::create([
                            'progress_id' => $progress->id,

                            'start_date' => $progress->keiyaku_shiki_date,
                            'start_date_state' => $progress->keiyaku_shiki_date ? 1 : 0,

                            'application_date' => $progress->mousikomi_date,
                            'application_date_state' => $progress->mousikomi_date ? 1 : 0,

                            'guarantee_screening_date' => $progress->nyuukyo_examination_company_date,
                            'guarantee_screening_date_state' => $progress->nyuukyo_examination_company_date ? 1 : 0,

                            'wp_screening_date' => $progress->nyuukyo_examination_pm_date,
                            'wp_screening_date_state' => $progress->nyuukyo_examination_pm_date ? 1 : 0,

                            'owner_reported_date' => $progress->nyuukyo_examination_owner_date,
                            'owner_reported_date_state' => $progress->nyuukyo_examination_owner_date ? 1 : 0,

                            'owner_approved_date' => $progress->owner_shoudaku_date,
                            'owner_approved_date_state' => $progress->owner_shoudaku_date ? 1 : 0,

                            'key_requested_date' => $progress->keiyaku_key_exchange_date,
                            'key_requested_date_state' => $progress->keiyaku_key_exchange_date ? 1 : 0,

                            'invoice_issued_date' => $progress->keiyaku_seikyu_date,
                            'invoice_issued_date_state' => $progress->keiyaku_seikyu_date ? 1 : 0,

                            'contract_sent_date' => $progress->keiyaku_hassou_date,
                            'contract_sent_date_state' => $progress->keiyaku_hassou_date ? 1 : 0,

                            'contract_payment_date' => $progress->keiyaku_nyukin_date,
                            'contract_payment_date_state' => $progress->keiyaku_nyukin_date ? 1 : 0,

                            'contract_collected_date' => $progress->keiyaku_collect_date,
                            'contract_collected_date_state' => $progress->keiyaku_collect_date ? 1 : 0,

                            'electricity_cancellation_date' => $progress->keiyaku_denki_kaiyaku_date,
                            'electricity_cancellation_date_state' => $progress->keiyaku_denki_kaiyaku_date ? 1 : 0,

                            'key_handover_date' => $progress->keiyaku_key_date,
                            'key_handover_date_state' => $progress->keiyaku_key_date ? 1 : 0,

                            'documents_archived_date' => $progress->keiyaku_date,
                            'documents_archived_date_state' => $progress->keiyaku_date ? 1 : 0,

                            'completed_date' => $progress->complete_date,
                            'completed_date_state' => $progress->complete_date ? 1 : 0,
                        ]);
                        $enProgress->resetNextAction();
                        $enProgress->save();
                    }
                });
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('en_progresses');
    }
};
