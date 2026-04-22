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
            $table->renameColumn('completion_message', 'construction_ccompletion_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ge_progresses', function (Blueprint $table) {
            $table->renameColumn('construction_ccompletion_message', 'completion_message');
        });
    }
};
