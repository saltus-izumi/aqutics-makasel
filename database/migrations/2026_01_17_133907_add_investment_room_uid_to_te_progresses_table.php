<?php

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
        Schema::table('te_progresses', function (Blueprint $table) {
            $table->integer('investment_room_uid')->nullable()->comment('物件ルームID')->after('investment_id');
        });

        DB::statement(
            'UPDATE te_progresses tp
             INNER JOIN investment_rooms ir
             ON tp.investment_id = ir.investment_id
             AND tp.investment_room_id = ir.investment_room_id
             SET tp.investment_room_uid = ir.id'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('te_progresses', function (Blueprint $table) {
            $table->dropColumn('investment_room_uid');
        });
    }
};
