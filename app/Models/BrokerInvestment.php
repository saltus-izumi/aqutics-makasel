<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use Illuminate\Database\Eloquent\Model;

class BrokerInvestment extends Model
{
    use RecordsUserStamps;

    protected $table = 'broker_investments';

    protected $guarded = [
        'id',
    ];

    public const CREATED_AT = 'created';
    public const UPDATED_AT = 'modified';
}
