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
                'symbol' => 'ETHUSDT',
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
                'symbol' => 'ETHUSDT'
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
                'symbol' => 'ETHUSDT',
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

    public function orders(string $symbol = 'ETHUSDT', $limit = 500, $fromOrderId = 0, $params = []): array
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
    public function allOrders(string $symbol = 'ETHUSDT', ?string $updateTime = null): array
    {
        return $this->httpRequest(
            'fapi/v1/allOrders',
            'GET',
            [
                'fapi' => true,
                'symbol' => $symbol,
                'startTime' => $updateTime - 1000
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
                'symbol' => 'ETHUSDT'
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
                'symbol' => 'ETHUSDT',
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
                'symbol' => 'ETHUSDT',
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

    /**
     * @throws Exception
     */
    public function openShort(float $size, float $entry): array
    {
        return $this->httpRequest(
            'fapi/v1/order',
            'POST',
            [
                'fapi' => true,
                'symbol' => 'ETHUSDT',
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

    /**
     * @throws Exception
     */
    public function closeShort(float $size, float $entry): array
    {
        return $this->httpRequest(
            'fapi/v1/order',
            'POST',
            [
                'fapi' => true,
                'symbol' => 'ETHUSDT',
                'side' => 'BUY',
                'quantity' => $size,
                'type' => 'LIMIT',
                'price' => $entry,
                'timeInForce' => 'GTC',
                'positionSide' => 'SHORT'
            ],
            true
        );
    }

    /**
     * @throws Exception
     */
    public function income($time = null): array
    {
        return $this->httpRequest(
            'fapi/v1/income',
            'GET',
            [
                'fapi' => true,
                'symbol' => 'ETHUSDT',
                'incomeType' => 'FUNDING_FEE',
                'startTime' => $time
            ],
            true
        );
    }
}