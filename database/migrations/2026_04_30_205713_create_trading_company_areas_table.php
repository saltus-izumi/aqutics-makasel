<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trading_company_areas', function (Blueprint $table) {
            $table->id();
            $table->integer('trading_company_id')->nullable()->comment('取引先会社ID');
            $table->integer('address_area_id')->nullable()->comment('住所エリアID');

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

        DB::table('trading_companies')
            ->select([
                'id',
                'area',
                'created_user_id',
                'user_created_at',
                'updated_user_id',
                'user_updated_at',
                'deleted_user_id',
                'user_deleted_at',
                'created_at',
                'updated_at',
                'deleted_at',
            ])
            ->orderBy('id')
            ->chunkById(200, function ($tradingCompanies): void {
                $insertRows = [];

                foreach ($tradingCompanies as $tradingCompany) {
                    if (!is_string($tradingCompany->area) || trim($tradingCompany->area) === '') {
                        continue;
                    }

                    $areaIds = array_unique(array_filter(array_map(static function ($areaId) {
                        $trimmedAreaId = trim((string) $areaId);

                        if ($trimmedAreaId === '' || !ctype_digit($trimmedAreaId)) {
                            return null;
                        }

                        return (int) $trimmedAreaId;
                    }, explode(',', str_replace('，', ',', $tradingCompany->area)))));

                    foreach ($areaIds as $areaId) {
                        $insertRows[] = [
                            'trading_company_id' => $tradingCompany->id,
                            'address_area_id' => $areaId,
                            'created_user_id' => $tradingCompany->created_user_id,
                            'user_created_at' => $tradingCompany->user_created_at,
                            'updated_user_id' => $tradingCompany->updated_user_id,
                            'user_updated_at' => $tradingCompany->user_updated_at,
                            'deleted_user_id' => $tradingCompany->deleted_user_id,
                            'user_deleted_at' => $tradingCompany->user_deleted_at,
                            'created_at' => $tradingCompany->created_at,
                            'updated_at' => $tradingCompany->updated_at,
                            'deleted_at' => $tradingCompany->deleted_at,
                        ];
                    }
                }

                if ($insertRows === []) {
                    return;
                }

                DB::table('trading_company_areas')->insert($insertRows);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trading_company_areas');
    }
};
