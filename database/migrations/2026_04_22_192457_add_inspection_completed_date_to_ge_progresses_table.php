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
            $table->date('inspection_request_date')->nullable()->comment('立会依頼日')->after('move_out_report_date');
            $table->date('inspection_completed_date')->nullable()->comment('実行担当判断日')->after('inspection_completed_message');
            $table->date('cost_registration_completed_date')->nullable()->comment('上下代登録完了日')->after('executor_to_responsible_message');
            $table->date('owner_eligibility_decision_date')->nullable()->comment('上下代登録完了日')->after('estimate_note_message');
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ge_progresses', function (Blueprint $table) {
            $table->dropColumn('inspection_request_date');
            $table->dropColumn('inspection_completed_date');
            $table->dropColumn('cost_registration_completed_date');
            $table->dropColumn('owner_eligibility_decision_date');
        });
    }
};
