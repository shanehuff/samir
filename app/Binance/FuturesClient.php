<?php

namespace App\Binance;

use Binance\API;
use Exception;
use Illuminate\Support\Collection;

class FuturesClient extends API
{
    public function account(): array
    {
        return $this->httpRequest(
            'fapi/v2/account',
            'GET',
            [
                'fapi' => true
            ],
            true
        );
    }

    /**
     * @throws Exception
     */
    public function balance(): array
    {
        return $this->httpRequest(
            'fapi/v2/balance',
            'GET',
            [
                'fapi' => true
            ],
            true
        );
    }

    /**
     * @throws Exception
     */
    public function userTrades($startTime = null): array
    {
        return $this->httpRequest(
            'fapi/v1/userTrades',
            'GET',
            [
                'fapi' => true,
                'symbol' => 'BNBBUSD',
                'startTime' => $startTime
            ],
            true
        );
    }

    /**
     * @throws Exception
     */
    public function positions(): Collection
    {
        return collect($this->httpRequest(
            'fapi/v2/positionRisk',
            'GET',
            [
                'fapi' => true,
                'symbol' => 'BNBBUSD'
            ],
            true
        ));
    }

    /**
     * @throws Exception
     */
    public function openLong(float $size, float $entry): array
    {
        return $this->httpRequest(
            'fapi/v1/order',
            'POST',
            [
                'fapi' => true,
                'symbol' => 'BNBBUSD',
                'side' => 'BUY',
                'quantity' => $size,
                'type' => 'LIMIT',
                'price' => $entry,
                'timeInForce' => 'GTC',
                'positionSide' => 'LONG'
            ],
            true
        );
    }

    public function orders(string $symbol = 'BNBBUSD', $limit = 500, $fromOrderId = 0, $params = []): array
    {
        return $this->httpRequest(
            'fapi/v1/openOrders',
            'GET',
            [
                'fapi' => true,
                'symbol' => $symbol
            ],
            true
        );
    }

    /**
     * @throws Exception
     */
    public function cancelAllOrders(): array
    {
        return $this->httpRequest(
            'fapi/v1/allOpenOrders',
            'DELETE',
            [
                'fapi' => true,
                'symbol' => 'BNBBUSD'
            ],
            true
        );
    }

    /**
     * @throws Exception
     */
    public function getOrder($orderId): array
    {
        return $this->httpRequest(
            'fapi/v1/order',
            'GET',
            [
                'fapi' => true,
                'symbol' => 'BNBBUSD',
                'orderId' => $orderId
            ],
            true
        );
    }

    public function income()
    {
//        return $this->httpRequest(
//            'fapi/v1/order',
//            'GET',
//            [
//                'fapi' => true,
//                'symbol' => 'BNBBUSD',
//                'orderId' => $orderId
//            ],
//            true
//        );
    }
}