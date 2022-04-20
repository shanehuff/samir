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
            'payloads' => 'required|array'
        ]);

        if($this->shouldUseV2($request)) {
            $this->createTradingviewAlertV2(
                $request->input('payloads.resolution'),
                $request->input('payloads.stochastic'),
                (float)$request->input('payloads.price')
            );
        }else{
            $this->createTradingviewAlert(
                $request->input('payloads.side'),
                $request->input('payloads.timeframe'),
                (float)$request->input('payloads.price')
            );
        }


        return ['status' => 'success'];
    }

    private function shouldUseV2(Request $request): bool
    {
        return $request->has('payloads.resolution');
    }
}
