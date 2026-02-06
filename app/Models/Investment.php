<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Investment extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    /**
     * この物件が属する家主を取得
     */
    public function landlord()
    {
        return $this->belongsTo(Landlord::class);
    }

    /**
     * この物件が持つ部屋（InvestmentRoom）を取得
     */
    public function investmentRooms()
    {
        return $this->hasMany(InvestmentRoom::class);
    }

    public function restorationCompany()
    {
        return $this->belongsTo(TradingCompany::class);
    }


    /**
     * オーナーに紐づく物件のオプションを取得（Owner→Landlord→Investment）
     *
     * @param int|string $ownerId
     * @return array
     */
    public static function getOptionsByOwner($ownerId): array
    {
        $owner = Owner::with(['landlords.investments' => function ($query) {
            $query->where('is_display', true)->orderBy('id');
        }])->find($ownerId);

        $options = [];
        if ($owner) {
            foreach ($owner->landlords as $landlord) {
                foreach ($landlord->investments as $investment) {
                    $options[$investment->id] = $investment->id . ':' . $investment->investment_name;
                }
            }
        }

        return $options;
    }

    /**
     * 全物件のオプションを取得
     *
     * @return array
     */
    public static function getOptions(): array
    {
        $investments = self::where('is_display', 1)
            ->orderBy('id')
            ->get();

        $options = [];
        foreach ($investments as $investment) {
            $options[$investment->id] = $investment->id . ':' . $investment->investment_name;
        }

        return $options;
    }

    /****************
    * プロコールからの物件名でデータを取得する
    *
    * 取得ルール
    *   以下の順で取得を試す
    *   1. そのままの名称で取得
    *   2. 半角変換可能なものを半角変換して検索
    *   3. （旧 〜）と記載されている可能性があるので、データを分割して検索
    ****************/
    public static function getByInvestmentNameForProcall($investmentName) {
        // そのままの名称で検索
        $investment = self::where('investment_name', $investmentName)
            ->first();
        if ($investment) return $investment;

        // 半角変換可能なものを半角に変換して検索
        $investment = self::where('investment_name', mb_convert_kana($investmentName, 'r'))
            ->first();
        if ($investment) return $investment;

        // （旧 〜）と記載されている可能性があるので文字列を分割してからそれぞれの要素を検索
        if (preg_match('/(.*)（旧　(.*)）/', $investmentName, $matches)) {
            for ($i = 1; $i < count($matches); $i++) {
                // そのままの名称で検索
                $investment = self::where('investment_name', $matches[$i])
                    ->first();
                if ($investment) return $investment;

                // 半角変換可能なものを半角に変換して検索
                $investment = self::where('investment_name', mb_convert_kana($matches[$i], 'r'))
                    ->first();
                if ($investment) return $investment;
            }
        }

        return null;
    }

}
