<?php

namespace App\Http\Controllers\Api;

use App\Dealers\Dealer;
use App\Http\Controllers\Controller;
use App\Tradingview\InteractsWithTradingviewAlerts;
use Illuminate\Http\Request;

class HandleTradingviewHookController extends Controller
{
    use InteractsWithTradingviewAlerts;

    public function __invoke(Request $request): array
    {
        $request->validate([
            'payloads' => 'required|array'
        ]);

        info(json_encode($request->payloads));

        if('down' === $request->payloads['direction']) {
            Dealer::openLongOrUpdate();
        }

        if('up' === $request->payloads['direction']) {
            Dealer::takeProfitOrCancel();
        }

        return [
            'success' => true
        ];
    }
}
