<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeProgress extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    protected $table = 'te_progresses';

    protected $guarded = [
        'id'
    ];

    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }

    public function investmentRoom()
    {
        return $this->belongsTo(InvestmentRoom::class, 'investment_room_uid', 'id');
    }

    // 上代見積り
    public function retailEstimateFiles()
    {
        return $this->hasMany(TeProgressFile::class)
            ->where('file_kind', TeProgressFile::FILE_KIND_RETAIL_ESTIMATE);
    }

    // 下代見積り
    public function lowerEstimateFiles()
    {
        return $this->hasMany(TeProgressFile::class)
            ->where('file_kind', TeProgressFile::FILE_KIND_LOWER_ESTIMATE);
    }

    // 発注書
    public function purchaseOrderFiles()
    {
        return $this->hasMany(TeProgressFile::class)
            ->where('file_kind', TeProgressFile::FILE_KIND_PURCHASE_ORDER);
    }

    // 現調報告書
    public function onSiteInspectionReportFiles()
    {
        return $this->hasMany(TeProgressFile::class)
            ->where('file_kind', TeProgressFile::FILE_KIND_ON_SITE_INSPECTION_REPORT);
    }

    // 完工写真
    public function completionPhotoFiles()
    {
        return $this->hasMany(TeProgressFile::class)
            ->where('file_kind', TeProgressFile::FILE_KIND_COMPLETION_PHOTO);
    }
}
