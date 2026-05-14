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
        Schema::table('trading_companies', function (Blueprint $table) {
            $table->text('emergency_contact')->nullable()->comment('緊急連絡先')->after('tel');
            $table->text('business_hours')->nullable()->comment('営業時間')->after('mail');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trading_companies', function (Blueprint $table) {
            $table->dropColumn('emergency_contact');
            $table->dropColumn('business_hours');
        });
    }
};
