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
        Schema::create('en_progress_individual_applicants', function (Blueprint $table) {
            $table->increments('id')->comment('EN進捗申込人ID');
            $table->integer('en_progress_id')->nullable()->comment('EN進捗ID');
            $table->string('last_name')->nullable()->comment('氏名（姓）');
            $table->string('first_name')->nullable()->comment('氏名（名）');
            $table->string('last_kana')->nullable()->comment('カナ（姓）');
            $table->string('first_kana')->nullable()->comment('カナ（名）');
            $table->integer('gender')->nullable()->comment('性別');
            $table->date('birth_date')->nullable()->comment('生年月日');
            $table->boolean('spouse_flag')->nullable()->comment('配偶者');
            $table->string('phone_number')->nullable()->comment('電話番号');
            $table->string('mobile_phone_number')->nullable()->comment('携帯電話番号');
            $table->string('email')->nullable()->comment('メールアドレス');
            $table->string('postal_code')->nullable()->comment('郵便番号');
            $table->string('prefecture')->nullable()->comment('都道府県');
            $table->string('city')->nullable()->comment('市区町村');
            $table->string('street')->nullable()->comment('番地・丁目');
            $table->string('building')->nullable()->comment('建物名・部屋番号');
            $table->string('residence_type')->nullable()->comment('居住種別');
            $table->string('residence_years')->nullable()->comment('居住年数');
            $table->string('move_reason')->nullable()->comment('転居理由');
            $table->string('moving_guidance')->nullable()->comment('引越し案内');
            $table->string('occupation')->nullable()->comment('職業');
            $table->string('workplace_name')->nullable()->comment('勤務先/学校名');
            $table->string('workplace_kana')->nullable()->comment('勤務先・カナ');
            $table->string('workplace_phone_number')->nullable()->comment('勤務先・電話番号');
            $table->string('workplace_postal_code')->nullable()->comment('勤務先・郵便番号');
            $table->string('workplace_prefecture')->nullable()->comment('勤務先・都道府県');
            $table->string('workplace_city')->nullable()->comment('勤務先・市区町村');
            $table->string('workplace_street')->nullable()->comment('勤務先・番地・丁目');
            $table->string('workplace_building')->nullable()->comment('勤務先・建物名・部屋番号');
            $table->string('industry')->nullable()->comment('業種');
            $table->string('years_of_service')->nullable()->comment('勤続年数');
            $table->integer('annual_income')->nullable()->comment('税込年収');
            $table->integer('capital')->nullable()->comment('資本金');
            $table->integer('number_of_employees')->nullable()->comment('従業員数');
            $table->date('established_date')->nullable()->comment('設立年月日');
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
        Schema::dropIfExists('en_progress_individual_applicants');
    }
};
