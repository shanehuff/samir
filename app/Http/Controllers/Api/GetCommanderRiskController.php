<?php

namespace App\Http\Controllers\Api;

use App\Commander\InteractsWithCommanders;
use App\Http\Controllers\Controller;
use App\Models\Commander;
use Illuminate\Http\JsonResponse;

class GetCommanderRiskController extends Controller
{
    use InteractsWithCommanders;

    public function __invoke(int $id): JsonResponse
    {
        /** @var Commander $commander */
        $commander = $this->findCommanderOrFail($id);
        $risk = $commander->getRisk();

        return response()->json([
            'commander_id' => $commander->id,
            'leverage' => $risk->getLeverage(),
            'balance' => $commander->fund,
            'liquidation_price' => round($risk->getLiquidationPrice(), 2)
        ]);
    }
}
