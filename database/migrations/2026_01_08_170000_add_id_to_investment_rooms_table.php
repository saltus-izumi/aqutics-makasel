<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('investment_rooms', 'id')) {
            return;
        }

        DB::statement(
            'ALTER TABLE investment_rooms ' .
            'ADD COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT FIRST, ' .
            'ADD UNIQUE KEY investment_rooms_id_unique (id)'
        );
    }

    public function down(): void
    {
        if (!Schema::hasColumn('investment_rooms', 'id')) {
            return;
        }

        DB::statement(
            'ALTER TABLE investment_rooms ' .
            'DROP INDEX investment_rooms_id_unique, ' .
            'DROP COLUMN id'
        );
    }
};
