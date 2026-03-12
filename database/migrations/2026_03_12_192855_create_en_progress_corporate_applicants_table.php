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
        Schema::create('en_progress_corporate_applicants', function (Blueprint $table) {
            $table->increments('id')->comment('EN進捗申込人ID');
            $table->integer('en_progress_id')->nullable()->comment('EN進捗ID');
            $table->string('company_name')->nullable()->comment('会社名');
            $table->string('company_kana')->nullable()->comment('会社名（カナ）');
            $table->string('head_office_postal_code')->nullable()->comment('本社所在地（郵便番号）');
            $table->string('head_office_prefecture')->nullable()->comment('本社所在地（都道府県）');
            $table->string('head_office_city')->nullable()->comment('本社所在地（市区町村）');
            $table->string('head_office_street')->nullable()->comment('本社所在地（番地・丁目）');
            $table->string('head_office_building')->nullable()->comment('本社所在地（建物名・部屋番号）');
            $table->string('head_office_phone_number')->nullable()->comment('本社電話番号');
            $table->string('head_office_fax_number')->nullable()->comment('本社FAX番号');
            $table->string('email')->nullable()->comment('申込者メールアドレス');
            $table->string('industry')->nullable()->comment('業種');
            $table->integer('capital')->nullable()->comment('資本金');
            $table->integer('number_of_employees')->nullable()->comment('従業員数');
            $table->date('established_date')->nullable()->comment('設立年月日');
            $table->string('representative_last_name')->nullable()->comment('代表者氏名（名字）');
            $table->string('representative_first_name')->nullable()->comment('代表者氏名（名前）');
            $table->string('representative_last_kana')->nullable()->comment('代表者氏名（名字カナ）');
            $table->string('representative_first_kana')->nullable()->comment('代表者氏名（名前カナ）');
            $table->string('representative_mobile_phone_number')->nullable()->comment('代表者携帯電話番号');
            $table->string('representative_postal_code')->nullable()->comment('代表者現住所（郵便番号）');
            $table->string('representative_prefecture')->nullable()->comment('代表者現住所（都道府県）');
            $table->string('representative_city')->nullable()->comment('代表者現住所（市区町村）');
            $table->string('representative_street')->nullable()->comment('代表者現住所（番地・丁目）');
            $table->string('representative_building')->nullable()->comment('代表者現住所（建物名・部屋番号）');
            $table->string('contact_last_name')->nullable()->comment('担当者名（名字）');
            $table->string('contact_first_name')->nullable()->comment('担当者名（名前）');
            $table->string('contact_last_kana')->nullable()->comment('担当者名（名字カナ）');
            $table->string('contact_first_kana')->nullable()->comment('担当者名（名前カナ）');
            $table->string('contact_department')->nullable()->comment('担当者所属部署');
            $table->string('contact_phone_number')->nullable()->comment('担当者電話番号');
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
        Schema::dropIfExists('en_progress_corporate_applicants');
    }
};
