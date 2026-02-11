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
            $table->integer('is_proper_work_burden')->nullable()->comment('適正工事（負担）')->after('responsible_person_message');
            $table->integer('is_proper_price')->nullable()->comment('適正価格')->after('is_proper_work_burden');
            $table->text('correction_instruction_message')->nullable()->comment('実行担当へ修正指示')->after('is_proper_price');
            $table->text('estimate_note_message')->nullable()->comment('見積書備考入力内容')->after('correction_instruction_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ge_progresses', function (Blueprint $table) {
            $table->dropColumn('is_proper_work_burden');
            $table->dropColumn('is_proper_price');
            $table->dropColumn('correction_instruction_message');
            $table->dropColumn('estimate_note_message');
        });
    }
};
