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
        Schema::table('te_progresses', function (Blueprint $table) {
            if (!Schema::hasColumn('te_progresses', 'te_id')) {
                $table->string('te_id')->nullable()->comment('TE_ID')->after('id');
            }
            if (!Schema::hasColumn('te_progresses', 'is_reproposed')) {
                $table->boolean('is_reproposed')->default(false)->comment('再提案済み');
            }
            if (!Schema::hasColumn('te_progresses', 'repropose_count')) {
                $table->integer('repropose_count')->default(0)->comment('再提案連番');
            }
            if (!Schema::hasColumn('te_progresses', 'root_te_progress_id')) {
                $table->integer('root_te_progress_id')->nullable()->comment('親プロセスID');
            }
            if (!Schema::hasColumn('te_progresses', 'operation_thread_id')) {
                $table->integer('operation_thread_id')->nullable()->comment('オペレーションスレッドID');
            }
            if (!Schema::hasColumn('te_progresses', 'operation_id')) {
                $table->integer('operation_id')->nullable()->comment('オペレーションID');
            }
            if (!Schema::hasColumn('te_progresses', 'created_user_id')) {
                $table->integer('created_user_id')->nullable()->comment('データ登録スタッフID');
            }
            if (!Schema::hasColumn('te_progresses', 'user_created_at')) {
                $table->datetime('user_created_at')->nullable()->comment('データ登録日時（スタッフ）');
            }
            if (!Schema::hasColumn('te_progresses', 'updated_user_id')) {
                $table->integer('updated_user_id')->nullable()->comment('データ更新スタッフID');
            }
            if (!Schema::hasColumn('te_progresses', 'user_updated_at')) {
                $table->datetime('user_updated_at')->nullable()->comment('データ更新日時（スタッフ）');
            }
            if (!Schema::hasColumn('te_progresses', 'deleted_user_id')) {
                $table->integer('deleted_user_id')->nullable()->comment('データ削除スタッフID');
            }
            if (!Schema::hasColumn('te_progresses', 'user_deleted_at')) {
                $table->datetime('user_deleted_at')->nullable()->comment('データ削除日時（スタッフ）');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('te_progresses', function (Blueprint $table) {
            foreach ([
                'created_user_id',
                'user_created_at',
                'updated_user_id',
                'user_updated_at',
                'deleted_user_id',
                'user_deleted_at',
            ] as $column) {
                if (Schema::hasColumn('te_progresses', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
