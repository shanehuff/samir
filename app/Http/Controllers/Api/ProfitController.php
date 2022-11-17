<?php

namespace App\Http\Controllers\Api;

use App\Dealers\DealerProfit;
use App\Services\Keisha;

class ProfitController
{
    private int $vnd = 0;

    public function __construct()
    {
        $this->vnd = (int) (new Keisha())->getPricing()->get('BUSD');
    }

    public function __invoke(): array
    {
        $profits = DealerProfit::all();

        return [
            'net_profit' => $this->toVND($profits->sum('net_profit')),
            'fee' => $this->toVND($profits->sum('fee')),
            'avg_roe' => number_format($profits->avg('roe') * 100, 2),
            'avg_duration' => number_format($profits->avg('duration') / 60, 2),
            'deals_count' => $profits->count()
        ];
    }

    private function toVND($amount): string
    {
        return number_format($amount * $this->vnd, 0);
    }
}