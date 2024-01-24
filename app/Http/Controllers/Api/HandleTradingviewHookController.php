<?php

namespace App\Http\Controllers\Api;

use App\Trading\Champion;
use App\Trading\TradingManager;
use App\Trading\SpotTradingManager;
use App\Http\Controllers\Controller;
use App\Tradingview\InteractsWithTradingviewAlerts;
use Exception;
use Illuminate\Http\Request;

class HandleTradingviewHookController extends Controller
{
    use InteractsWithTradingviewAlerts;

    /**
     * @throws Exception
     */
    public function __invoke(Request $request): array
    {
        $request->validate([
            'payloads' => 'required|array'
        ]);

        info(json_encode($request->payloads));

        if ('SPOTRSI5' === $request->payloads['indicator']) {
            $this->handleSpotTrades($request);
        } else {
            $this->handleFuturesTrades($request);
        }

        return [
            'success' => true
        ];
    }

    private function handleFuturesTrades(Request $request)
    {
        $champions = Champion::query()
            ->where('archetype', 'farmer')
            ->where('status', 'active')
            ->get();

        info('Champions: ' . $champions->count());

        if ($champions->count() > 0) {
            $champions->each(function ($champion) use ($request) {
                if ($champion->symbol === $request->payloads['symbol']) {
                    TradingManager::useChampion($champion);

                    TradingManager::importRecentOrders();

                    if ('down' === $request->payloads['direction']) {
                        TradingManager::handleDown();
                    }

                    if ('up' === $request->payloads['direction']) {
                        TradingManager::handleUp();
                    }
                }
            });
        }
    }

    private function handleSpotTrades(Request $request)
    {
        $champions = Champion::query()
            ->where('archetype', 'lootcycle')
            ->where('status', 'active')
            ->get();

        $spotTradingManager = new SpotTradingManager();

        if ($champions->count() > 0) {
            $champions->each(function ($champion) use ($request, $spotTradingManager) {
                if ($champion->symbol === $request->payloads['symbol']) {
                    $spotTradingManager->useChampion($champion);
                    $spotTradingManager->syncOrdersFromExchange();

                    //dd('down' === $request->payloads['direction'],$champion->can_buy_spot,$spotTradingManager->noRecentBuySpotOrder($champion));
                    if ('down' === $request->payloads['direction'] && $champion->can_buy_spot && $spotTradingManager->noRecentBuySpotOrder($champion)) {
                        $spotTradingManager->placeBuyOrder((float)$request->payloads['price']);
                    }

                    if ('up' === $request->payloads['direction'] && $champion->can_sell_spot && $spotTradingManager->noRecentSellSpotOrder($champion)) {
                        $spotTradingManager->maybePlaceSellOrder((float)$request->payloads['price']);
                    }
                }
            });
        }
    }
}
