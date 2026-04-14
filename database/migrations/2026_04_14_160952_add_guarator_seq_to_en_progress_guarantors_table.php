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
        Schema::table('en_progress_guarantors', function (Blueprint $table) {
            $table->integer('guarator_seq')->default(1)->comment('連帯保証人連番')->after('en_progress_id');
            $table->string('email')->nullable()->comment('メールアドレス')->after('phone_number');
            $table->string('income_day')->nullable()->comment('収入日')->after('established_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('en_progress_guarantors', function (Blueprint $table) {
            $table->dropColumn('guarator_seq');
            $table->dropColumn('email');
            $table->dropColumn('income_day');
        });
    }
};
