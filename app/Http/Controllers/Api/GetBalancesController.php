<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class GetBalancesController
{
    public function __invoke(): JsonResponse
    {
        $balances = DB::table('balances')->get();
        $balances = $balances->map(function($balance) {
            return [
                'symbol' => $balance->symbol,
                'amount' => $balance->available + $balance->on_order
            ];
        });

        return response()->json($balances);
    }
}