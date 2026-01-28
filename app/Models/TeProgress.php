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

    public const LAST_IMPORT_KIND_NEW = 1;
    public const LAST_IMPORT_KIND_UPDATE = 2;
    public const LAST_IMPORT_KIND = [
        self::LAST_IMPORT_KIND_NEW => '新規',
        self::LAST_IMPORT_KIND_UPDATE => '更新',
    ];

    protected $guarded = [
        'id'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nyuuden_date' => 'date',
            'complete_date' => 'date',
            'last_import_date' => 'datetime',
        ];
    }

    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }

    public function investmentRoom()
    {
        return $this->belongsTo(InvestmentRoom::class, 'investment_room_uid', 'id');
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id', 'id');
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
