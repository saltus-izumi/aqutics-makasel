<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradingCompanyRank extends Model
{
    protected $table = 'trading_company_ranks';

    protected $guarded = [
        'id',
    ];

    public $timestamps = false;

    public function tradingCompany()
    {
        return $this->belongsTo(TradingCompany::class, 'trading_company_id', 'id');
    }

    public function category1Master()
    {
        return $this->belongsTo(Category1Master::class, 'category1_master_id', 'id');
    }

    public function category2Master()
    {
        return $this->belongsTo(Category2Master::class, 'category2_master_id', 'id');
    }

    public function category3Master()
    {
        return $this->belongsTo(Category3Master::class, 'category3_master_id', 'id');
    }

    public function equipmentCategory1Master()
    {
        return $this->belongsTo(EquipmentCategory1Master::class, 'equipment_category1_master_id', 'id');
    }

    public function equipmentCategory2Master()
    {
        return $this->belongsTo(EquipmentCategory2Master::class, 'equipment_category2_master_id', 'id');
    }
}
