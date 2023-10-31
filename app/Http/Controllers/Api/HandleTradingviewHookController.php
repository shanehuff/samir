<?php

namespace App\Http\Controllers\Api;

use App\Trading\TradingManager;
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

        TradingManager::import();

        if('down' === $request->payloads['direction']) {
            TradingManager::handleDown();
        }

        if('up' === $request->payloads['direction']) {
            TradingManager::handleUp();
        }

        return [
            'success' => true
        ];
    }
}
