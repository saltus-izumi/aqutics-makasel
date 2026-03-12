<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('personal_tenancy_application_logs') && !Schema::hasTable('individual_tenancy_application_logs')) {
            Schema::rename('personal_tenancy_application_logs', 'individual_tenancy_application_logs');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('individual_tenancy_application_logs') && !Schema::hasTable('personal_tenancy_application_logs')) {
            Schema::rename('individual_tenancy_application_logs', 'personal_tenancy_application_logs');
        }
    }
};
