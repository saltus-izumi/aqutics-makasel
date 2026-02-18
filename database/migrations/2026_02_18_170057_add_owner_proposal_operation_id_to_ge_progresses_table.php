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
            $table->integer('owner_proposal_operation_id')->nullable()->comment('オーナー提案オペレーションID')->after('estimate_note_message');
            $table->integer('completion_report_operation_id')->nullable()->comment('完了報告オペレーションID')->after('restoration_company_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ge_progresses', function (Blueprint $table) {
            $table->dropColumn('owner_proposal_operation_id');
            $table->dropColumn('completion_report_operation_id');
        });
    }
};
