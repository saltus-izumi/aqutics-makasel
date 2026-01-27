<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('progresses', function (Blueprint $table) {
            $table->integer('investment_room_uid')->nullable()->comment('物件ルームID')->after('investment_id');
        });

        DB::statement(
            'UPDATE progresses pr
             INNER JOIN investment_rooms ir
             ON pr.investment_id = ir.investment_id
             AND pr.investment_room_id = ir.investment_room_id
             SET pr.investment_room_uid = ir.id'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progresses', function (Blueprint $table) {
            $table->dropColumn('investment_room_uid');
        });
    }
};
