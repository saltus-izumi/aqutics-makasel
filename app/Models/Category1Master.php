<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category1Master extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    public const INQUIRY = '1';
    public const CANCEL = '2';
    public const CANCEL_INQUIRY = '3';
    public const CLAIM_HARD = '4';
    public const CLAIM_SOFT = '5';
    public const OTHER = '6';

    public const SHORT_NAME = [
        self::CLAIM_HARD => 'H',
        self::CLAIM_SOFT => 'S',
        self::INQUIRY => '問',
        self::CANCEL_INQUIRY => '解問',
    ];


    protected function shortName(): Attribute
    {
        return Attribute::get(function() {
            return self::SHORT_NAME[$this->id] ?? '';
        });
    }

}
