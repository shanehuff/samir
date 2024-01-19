<?php

namespace App\Binance;

use Binance\API;
use Exception;
use Illuminate\Support\Collection;

class SpotClient extends API
{
    protected ?string $symbol = null;

    public function useSymbol($symbol): void
    {
        $this->symbol = $symbol;
    }

    public function test()
    {
        return $this->httpRequest(
            'v3/order',
            'POST',
            [
                'symbol' => 'BNBUSDT',
                'side' => 'BUY',
                'type' => 'LIMIT',
                'timeInForce' => 'GTC',
                'quantity' => 0.017,
                'price' => 311.6
            ],
            true
        );
    }
}