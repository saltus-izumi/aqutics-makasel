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
        Schema::table('te_progresses', function (Blueprint $table) {
            $table->integer('contractor_no')->nullable()->comment('契約者No')->after('procall_case_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('te_progresses', function (Blueprint $table) {
            $table->dropColumn('contractor_no');
        });
    }
};
