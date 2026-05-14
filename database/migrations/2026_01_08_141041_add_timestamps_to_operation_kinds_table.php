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
        if (!Schema::hasTable('operation_kinds')) {
            Schema::create('operation_kinds', function (Blueprint $table) {
                $table->id();
                $table->integer('operation_group_id')->nullable()->comment('オペレーショングループID');
                $table->text('value')->comment('オペレーション種別');
                $table->boolean('is_display')->default(true)->comment('表示フラグ');
                $table->boolean('deleted')->default(false)->comment('削除フラグ');
                $table->integer('created_uid')->nullable()->comment('作成者ID');
                $table->timestamp('created')->nullable()->comment('作成日');
                $table->integer('modified_uid')->nullable()->comment('更新者ID');
                $table->timestamp('modified')->nullable()->comment('更新日');
                $table->integer('created_user_id')->nullable()->comment('データ登録スタッフID');
                $table->datetime('user_created_at')->nullable()->comment('データ登録日時（スタッフ）');
                $table->integer('updated_user_id')->nullable()->comment('データ更新スタッフID');
                $table->datetime('user_updated_at')->nullable()->comment('データ更新日時（スタッフ）');
                $table->integer('deleted_user_id')->nullable()->comment('データ削除スタッフID');
                $table->datetime('user_deleted_at')->nullable()->comment('データ削除日時（スタッフ）');
                $table->timestamps();
                $table->softDeletes();
            });

            return;
        }

        Schema::table('operation_kinds', function (Blueprint $table) {
            if (!Schema::hasColumn('operation_kinds', 'operation_group_id')) {
                $table->integer('operation_group_id')->nullable()->comment('オペレーショングループID')->after('id');
            }
            if (!Schema::hasColumn('operation_kinds', 'created_user_id')) {
                $table->integer('created_user_id')->nullable()->comment('データ登録スタッフID')->after('modified');
            }
            if (!Schema::hasColumn('operation_kinds', 'user_created_at')) {
                $table->datetime('user_created_at')->nullable()->comment('データ登録日時（スタッフ）')->after('created_user_id');
            }
            if (!Schema::hasColumn('operation_kinds', 'updated_user_id')) {
                $table->integer('updated_user_id')->nullable()->comment('データ更新スタッフID')->after('user_created_at');
            }
            if (!Schema::hasColumn('operation_kinds', 'user_updated_at')) {
                $table->datetime('user_updated_at')->nullable()->comment('データ更新日時（スタッフ）')->after('updated_user_id');
            }
            if (!Schema::hasColumn('operation_kinds', 'deleted_user_id')) {
                $table->integer('deleted_user_id')->nullable()->comment('データ削除スタッフID')->after('user_updated_at');
            }
            if (!Schema::hasColumn('operation_kinds', 'user_deleted_at')) {
                $table->datetime('user_deleted_at')->nullable()->comment('データ削除日時（スタッフ）')->after('deleted_user_id');
            }
            if (!Schema::hasColumn('operation_kinds', 'created_at')) {
                $table->timestamp('created_at')->nullable()->after('user_deleted_at');
            }
            if (!Schema::hasColumn('operation_kinds', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }
            if (!Schema::hasColumn('operation_kinds', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operation_kinds', function (Blueprint $table) {
            $table->dropColumn([
                'created_user_id',
                'user_created_at',
                'updated_user_id',
                'user_updated_at',
                'deleted_user_id',
                'user_deleted_at',
            ]);
            $table->dropTimestamps();
            $table->dropSoftDeletes();
        });
    }
};
