<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Tradingview\InteractsWithTradingviewAlerts;
use Illuminate\Http\Request;

class HandleTradingviewHookController extends Controller
{
    use InteractsWithTradingviewAlerts;

    public function __invoke(Request $request): array
    {
        info(sprintf(
            'HandleTradingviewHookController: Receive request from Tradingview with data: %s',
            json_encode($request->all())
        ));

        $request->validate([
            'payloads.side' => 'required|string',
            'payloads.timeframe' => 'required|string'
        ]);

        $alert = $this->createTradingviewAlert(
            $request->input('payloads.side'),
            $request->input('payloads.timeframe')
        );

        info(sprintf(
            'HandleTradingviewHookController: Created Tradingview alert entry: %s',
            $alert ?? $alert->toJson()
        ));

        return ['status' => 'success'];
    }
}
