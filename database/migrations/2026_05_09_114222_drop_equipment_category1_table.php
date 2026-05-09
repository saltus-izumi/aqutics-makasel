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
        Schema::table('trading_company_ranks', function (Blueprint $table) {
            if (Schema::hasColumn('trading_company_ranks', 'equipment_category1_master_id')) {
                $table->dropColumn('equipment_category1_master_id');
            }

            if (Schema::hasColumn('trading_company_ranks', 'equipment_category2_master_id')) {
                $table->dropColumn('equipment_category2_master_id');
            }
        });

        Schema::dropIfExists('equipment_category1_masters');
        Schema::dropIfExists('equipment_category2_masters');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
