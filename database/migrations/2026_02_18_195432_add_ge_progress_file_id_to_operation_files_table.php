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
        Schema::table('operation_files', function (Blueprint $table) {
            $table->integer('ge_progress_file_id')->nullable()->comment('原復プロセスファイルID')->after('te_progress_file_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operation_files', function (Blueprint $table) {
            $table->dropColumn('ge_progress_file_id');
        });
    }
};
