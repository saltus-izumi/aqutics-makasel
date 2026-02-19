<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            "ALTER TABLE ge_progresses
             CHANGE responsible_person_message responsible_user_message TEXT NULL COMMENT '実行担当 ⇒ 責任担当'"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(
            "ALTER TABLE ge_progresses
             CHANGE responsible_user_message responsible_person_message TEXT NULL COMMENT '実行担当 ⇒ 責任担当'"
        );
    }
};
