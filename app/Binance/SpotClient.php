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

    public function getOrderTrades($symbol, $orderId, $limit = 500)
    {
        $parameters = [
            "symbol" => $symbol,
            "limit" => $limit,
        ];
        if ($orderId > 0) {
            $parameters["orderId"] = $orderId;
        }

        return $this->httpRequest("v3/myTrades", "GET", $parameters, true);
    }
}