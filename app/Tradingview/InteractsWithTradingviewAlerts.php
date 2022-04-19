<?php

namespace App\Tradingview;

use App\Models\TradingviewAlert;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait InteractsWithTradingviewAlerts
{
    public function createTradingviewAlert(
        string $side,
        string $timeframe,
        float $price = 0.0,
        $status = TradingviewAlert::STATUS_PENDING
    ): Model|Builder
    {
        return TradingviewAlert::query()->create([
            'side' => $side,
            'timeframe' => $timeframe,
            'status' => $status,
            'price' => $price
        ]);
    }

    public function createTradingviewAlertV2(
        int $resolution,
        int $stochastic,
        float $price = 0.0,
        $status = TradingviewAlert::STATUS_PENDING
    ): Model|Builder
    {
        return TradingviewAlert::query()->create([
            'resolution' => $resolution,
            'stochastic' => $stochastic,
            'status' => $status,
            'price' => $price
        ]);
    }
}
