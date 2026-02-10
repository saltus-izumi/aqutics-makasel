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
            $table->integer('cost_amount')->nullable()->comment('下代')->after('completion_message');
            $table->integer('charge_amount')->nullable()->comment('上代')->after('cost_amount');
            $table->text('responsible_person_message')->nullable()->comment('実行担当 ⇒ 責任担当')->after('charge_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ge_progresses', function (Blueprint $table) {
            $table->dropColumn('cost_amount');
            $table->dropColumn('charge_amount');
            $table->dropColumn('responsible_person_message');
        });
    }
};
