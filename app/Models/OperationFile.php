<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class OperationFile extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    public const FILE_KIND_RETAIL_ESTIMATE = 1;
    public const FILE_KIND_LOWER_ESTIMATE = 2;
    public const FILE_KIND_PURCHASE_ORDER = 3;
    public const FILE_KIND_ON_SITE_INSPECTION_REPORT = 4;
    public const FILE_KIND_COMPLETION_PHOTO = 5;
    public const FILE_KIND_OTHER = 6;

    public const FILE_KIND = [
        self::FILE_KIND_RETAIL_ESTIMATE => '上代見積り',
        self::FILE_KIND_LOWER_ESTIMATE => '下代見積もり',
        self::FILE_KIND_PURCHASE_ORDER => '発注書',
        self::FILE_KIND_ON_SITE_INSPECTION_REPORT => '現調報告書',
        self::FILE_KIND_COMPLETION_PHOTO => '完工写真',
        self::FILE_KIND_OTHER => 'その他ファイル',
    ];

    protected $guarded = [
        'id'
    ];

    public function teProgressFile()
    {
        return $this->belongsTo(TeProgressFile::class);
    }

    public function geProgressFile()
    {
        return $this->belongsTo(GeProgressFile::class);
    }


    protected function mimeType(): Attribute
    {
        return Attribute::get(function () {
            $mime = null;

            if (Storage::disk('local')->exists($this->file_path)) {
                $fullPath = Storage::disk('local')->path($this->file_path);
                $mime = mime_content_type($fullPath) ?: 'image/jpeg';
            }

            return $mime;
        });
    }

}
