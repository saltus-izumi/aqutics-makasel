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
            $table->integer('security_deposit_amount')->nullable()->comment('敷金預託等')->after('next_action');
            $table->integer('prorated_rent_amount')->nullable()->comment('日割り家賃')->after('security_deposit_amount');
            $table->integer('penalty_forfeiture_amount')->nullable()->comment('違約金（償却）')->after('prorated_rent_amount');
            $table->text('inspection_request_message')->nullable()->comment('立会依頼メッセージ')->after('penalty_forfeiture_amount');
            $table->date('transfer_due_date')->nullable()->comment('振込期日')->after('inspection_request_message');
            $table->integer('subtotal_a_amount')->nullable()->comment('小計A')->after('transfer_due_date');
            $table->integer('subtotal_b_amount')->nullable()->comment('小計B')->after('subtotal_a_amount');
            $table->integer('subtotal_c_amount')->nullable()->comment('小計C')->after('subtotal_b_amount');
            $table->integer('other_amount')->nullable()->comment('その他金額')->after('subtotal_c_amount');
            $table->text('inspection_completed_message')->nullable()->comment('立会完了メッセージ')->after('other_amount');
            $table->text('completion_message')->nullable()->comment('完工メッセージ')->after('inspection_completed_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ge_progresses', function (Blueprint $table) {
            $table->dropColumn('security_deposit_amount');
            $table->dropColumn('prorated_rent_amount');
            $table->dropColumn('penalty_forfeiture_amount');
            $table->dropColumn('inspection_request_message');
            $table->dropColumn('transfer_due_date');
            $table->dropColumn('subtotal_a_amount');
            $table->dropColumn('subtotal_b_amount');
            $table->dropColumn('subtotal_c_amount');
            $table->dropColumn('other_amount');
            $table->dropColumn('inspection_completed_message');
            $table->dropColumn('completion_message');
        });
    }
};
