<?php

namespace App\Livewire\Admin\Progress\Ge\Concerns;

use App\Models\GeProgress;
use App\Models\TradingCompany;

trait BuildsGeProgressMailReplacements
{
    protected function buildGeProgressMailReplacements(GeProgress $geProgress, ?TradingCompany $tradingCompany): array
    {
        $roomNo = (string) ($geProgress->progress?->investmentRoom?->investment_room_number ?? '');
        if ($roomNo === '' && (int) ($geProgress->progress?->investment_room_uid ?? 0) === 0) {
            $roomNo = '共用部';
        }

        return [
            '##investment_name##' => (string) ($geProgress->progress?->investment?->investment_name ?? ''),
            '##room_no##' => $roomNo,
            '##trading_company##' => (string) ($tradingCompany?->name ?? ''),
        ];
    }
}
