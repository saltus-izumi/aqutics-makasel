<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Investment extends Model
{
    use SoftDeletes;

    /**
     * この物件が属する家主を取得
     */
    public function landlord()
    {
        return $this->belongsTo(Landlord::class);
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
}
