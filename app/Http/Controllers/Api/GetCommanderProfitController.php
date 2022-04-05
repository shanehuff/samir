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
        $profit = $commander->getProfit();

        return response()->json([
            'commander_id' => $commander->id,
            'profit' => $profit->getTotal(),
            'buy_size' => $profit->getBuySize(),
            'buy_entry' => $profit->getBuyEntry(),
            'sell_size' => $profit->getSellSize(),
            'sell_entry' => $profit->getSellEntry(),
            'daily_profit_percentage' => $profit->getDailyProfitPercentage()
        ]);
    }
}
