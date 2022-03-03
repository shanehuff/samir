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
        $request->validate([
            'payloads.side' => 'required|string',
            'payloads.timeframe' => 'required|string'
        ]);

        $alert = $this->createTradingviewAlert(
            $request->input('payloads.side'),
            $request->input('payloads.timeframe')
        );

        return ['status' => 'success'];
    }
}
