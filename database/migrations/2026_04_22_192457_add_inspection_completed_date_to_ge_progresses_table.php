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
            $table->date('inspection_completed_date')->nullable()->comment('立会完了日')->after('inspection_completed_message');
            $table->date('construction_completion_date')->nullable()->comment('工事完工日')->after('construction_ccompletion_message');
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ge_progresses', function (Blueprint $table) {
            $table->dropColumn('inspection_completed_date');
            $table->dropColumn('construction_completion_date');
        });
    }
};
