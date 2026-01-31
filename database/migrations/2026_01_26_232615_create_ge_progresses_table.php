<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use App\Models\Progress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ge_progresses', function (Blueprint $table) {
            $table->id();

            $table->integer('progress_id')->comment('進捗ID');
            $table->integer('executor_user_id')->nullable()->comment('実行者');
            $table->integer('next_action')->nullable()->comment('ネクストアクション');
            $table->integer('created_user_id')->nullable()->comment('データ登録スタッフID');
            $table->dateTime('user_created_at')->nullable()->comment('データ登録日時（ スタッフ）');
            $table->integer('updated_user_id')->nullable()->comment('データ更新スタッフID');
            $table->dateTime('user_updated_at')->nullable()->comment('データ更新日時（スタッフ）');
            $table->integer('deleted_user_id')->nullable()->comment('データ削除スタッフID');
            $table->dateTime('user_deleted_at')->nullable()->comment('データ削除日時（スタッフ）');

            $table->timestamps();
            $table->softDeletes();
        });


        DB::transaction(function() {
            Progress::query()
                ->with('investmentEmptyRoom')
                ->orderBy('id')
                ->chunkById(500, function ($progresses) {
                    foreach ($progresses as $progress) {
                        DB::table('ge_progresses')->insert([
                            'progress_id' => $progress->id,
                            'next_action' => $progress->ge_next_action,
                            'created_at' => $progress->getAttribute('created'),
                            'updated_at' => $progress->getAttribute('modified'),
                        ]);
                    }
                });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ge_progresses');
    }
};
