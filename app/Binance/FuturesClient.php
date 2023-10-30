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
    public function userTrades($orderId = null, $startTime = null): array
    {
        return $this->httpRequest(
            'fapi/v1/userTrades',
            'GET',
            [
                'fapi' => true,
                'symbol' => 'BNBUSDT',
                'orderId' => $orderId,
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
                'symbol' => 'BNBUSDT'
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
                'symbol' => 'BNBUSDT',
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

    public function orders(string $symbol = 'BNBUSDT', $limit = 500, $fromOrderId = 0, $params = []): array
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
    public function allOrders(string $symbol = 'BNBUSDT'): array
    {
        return $this->httpRequest(
            'fapi/v1/allOrders',
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
                'symbol' => 'BNBUSDT'
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
                'symbol' => 'BNBUSDT',
                'orderId' => $orderId
            ],
            true
        );
    }

    /**
     * @throws Exception
     */
    public function closeLong(float $size, float $entry): array
    {
        return $this->httpRequest(
            'fapi/v1/order',
            'POST',
            [
                'fapi' => true,
                'symbol' => 'BNBUSDT',
                'side' => 'SELL',
                'quantity' => $size,
                'type' => 'LIMIT',
                'price' => $entry,
                'timeInForce' => 'GTC',
                'positionSide' => 'LONG'
            ],
            true
        );
    }

    public function openShort(float $size, float $entry): array
    {
        return $this->httpRequest(
            'fapi/v1/order',
            'POST',
            [
                'fapi' => true,
                'symbol' => 'BNBUSDT',
                'side' => 'SELL',
                'quantity' => $size,
                'type' => 'LIMIT',
                'price' => $entry,
                'timeInForce' => 'GTC',
                'positionSide' => 'SHORT'
            ],
            true
        );
    }
}