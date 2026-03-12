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
        Schema::create('en_progress_emergency_contacts', function (Blueprint $table) {
            $table->increments('id')->comment('EN進捗申込人ID');
            $table->integer('en_progress_id')->nullable()->comment('EN進捗ID');
            $table->string('last_name')->nullable()->comment('緊急連絡先・氏名（姓）');
            $table->string('first_name')->nullable()->comment('緊急連絡先・氏名（名）');
            $table->string('last_kana')->nullable()->comment('緊急連絡先・カナ（姓）');
            $table->string('first_kana')->nullable()->comment('緊急連絡先・カナ（名）');
            $table->integer('gender')->nullable()->comment('緊急連絡先・性別');
            $table->date('birth_date')->nullable()->comment('緊急連絡先・生年月日');
            $table->string('relationship')->nullable()->comment('緊急連絡先・続柄');
            $table->string('postal_code')->nullable()->comment('緊急連絡先・郵便番号');
            $table->string('prefecture')->nullable()->comment('緊急連絡先・都道府県');
            $table->string('city')->nullable()->comment('緊急連絡先・市区町村');
            $table->string('street')->nullable()->comment('緊急連絡先・番地・丁目');
            $table->string('building')->nullable()->comment('緊急連絡先・建物名・部屋番号');
            $table->string('phone_number')->nullable()->comment('緊急連絡先・電話番号');
            $table->string('mobile_phone_number')->nullable()->comment('緊急連絡先・携帯電話番号');
            $table->string('workplace_or_school_name')->nullable()->comment('緊急連絡先・勤務先/学校名');
            $table->string('workplace_or_school_kana')->nullable()->comment('緊急連絡先・勤務先・カナ');
            $table->string('workplace_or_school_phone_number')->nullable()->comment('緊急連絡先・勤務先・電話番号');
            $table->integer('created_user_id')->nullable()->comment('データ登録スタッフID');
            $table->dateTime('user_created_at')->nullable()->comment('データ登録日時（ スタッフ）');
            $table->integer('updated_user_id')->nullable()->comment('データ更新スタッフID');
            $table->dateTime('user_updated_at')->nullable()->comment('データ更新日時（スタッフ）');
            $table->integer('deleted_user_id')->nullable()->comment('データ削除スタッフID');
            $table->dateTime('user_deleted_at')->nullable()->comment('データ削除日時（スタッフ）');
            $table->dateTime('created_at')->nullable()->comment('データ登録日時');
            $table->dateTime('updated_at')->nullable()->comment('データ更新日時');
            $table->dateTime('deleted_at')->nullable()->comment('データ削除日時');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('en_progress_emergency_contacts');
    }
};
