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
        if (Schema::hasTable('sso_tokens')) {
            return;
        }

        Schema::create('sso_tokens', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('users.id to log in as');
            $table->string('token_digest')->unique()->comment('sha256 hash of token + shared secret');
            $table->dateTime('expires_at');
            $table->boolean('consumed')->default(false);
            $table->dateTime('consumed_at')->nullable();
            $table->dateTime('created')->useCurrent();
            $table->dateTime('modified')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sso_tokens');
    }
};
