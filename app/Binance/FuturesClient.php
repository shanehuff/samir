<?php

namespace App\Binance;

use Binance\API;
use Exception;
use Illuminate\Support\Collection;

class FuturesClient extends API
{
    protected ?string $symbol = null;

    public function useSymbol($symbol): void
    {
        $this->symbol = $symbol;
    }

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
                'symbol' => $this->symbol,
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
                'symbol' => $this->symbol
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
                'symbol' => $this->symbol,
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

    public function orders(string $symbol = null, $limit = 500, $fromOrderId = 0, $params = []): array
    {
        return $this->httpRequest(
            'fapi/v1/openOrders',
            'GET',
            [
                'fapi' => true,
                'symbol' => $symbol ?: $this->symbol
            ],
            true
        );
    }

    /**
     * @throws Exception
     */
    public function allOrders(string $symbol = null, ?string $updateTime = null): array
    {
        return $this->httpRequest(
            'fapi/v1/allOrders',
            'GET',
            [
                'fapi' => true,
                'symbol' => $symbol ?: $this->symbol,
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
                'symbol' => $this->symbol
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
                'symbol' => $this->symbol,
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
                'symbol' => $this->symbol,
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
                'symbol' => $this->symbol,
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
                'symbol' => $this->symbol,
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
                'symbol' => $this->symbol,
                'incomeType' => 'FUNDING_FEE',
                'startTime' => $time
            ],
            true
        );
    }
}