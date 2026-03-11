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
        Schema::table('en_progresses', function (Blueprint $table) {
            $table->integer('applicant_type')->nullable()->comment('申込人種別')->after('priority_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('en_progresses', function (Blueprint $table) {
            $table->dropColumn('applicant_type');
        });
    }
};
