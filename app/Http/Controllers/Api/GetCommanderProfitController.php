<?php

namespace App\Http\Controllers\Api;

use App\Commander\InteractsWithCommanders;
use App\Http\Controllers\Controller;
use App\Models\Commander;
use Illuminate\Http\JsonResponse;

class GetCommanderProfitController extends Controller
{
    use InteractsWithCommanders;

    public function __invoke(int $id): JsonResponse
    {
        /** @var Commander $commander */
        $commander = $this->findCommanderOrFail($id);
        $position = $commander->getPosition();
        $profit = $commander->getProfit();

        return response()->json([
            'commander_id' => $commander->id,
            'profit' => $profit->getTotal(),
            'buy_size' => $position->getBuySize(),
            'buy_entry' => $position->getBuyEntry(),
            'sell_size' => $position->getSellSize(),
            'sell_entry' => $position->getSellEntry(),
            'daily_profit_percentage' => $profit->getDailyProfitPercentage(),
            'duration' => $position->getDuration(),
            'monthly_profit_percentage' => $profit->getMonthlyProfitPercentage(),
            'apy' => $profit->getApy()
        ]);
    }
}
