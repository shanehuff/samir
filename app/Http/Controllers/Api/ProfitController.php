<?php

namespace App\Http\Controllers\Api;

use App\Dealers\DealerProfit;

class ProfitController
{
    public function __invoke(): array
    {
        $profits = DealerProfit::all();

        return [
            'net_profit' => number_format($profits->sum('net_profit'), 2),
            'fee' => number_format($profits->sum('fee'), 2),
            'avg_roe' => number_format($profits->avg('roe') * 100, 2),
            'avg_duration' => number_format($profits->avg('duration') / 60, 2),
            'deals_count' => $profits->count()
        ];
    }
}