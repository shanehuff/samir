<?php

namespace App\Binance;

use Binance\API;
use Exception;

class ApiClient extends API
{
    /**
     * @throws Exception
     */
    public function saving(): ?array
    {
        return $this->httpRequest(
            'v1/staking/position',
            'GET',
            [
                'sapi' => true,
                'product' => 'STAKING'
            ],
            true
        );
    }

    /**
     * @throws Exception
     */
    public function farming(): ?array
    {
        return $this->httpRequest(
            'v1/bswap/liquidity',
            'GET',
            [
                'sapi' => true
            ],
            true
        );
    }

    public function loans()
    {
        return $this->httpRequest(
            'v1/loan/ongoing/orders',
            'GET',
            [
                'sapi' => true
            ],
            true
        );
    }

    public function futures()
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

    public function createFuturesOrder()
    {
        return $this->httpRequest(
            'fapi/v1/order',
            'POST',
            [
                'fapi' => true,
                'symbol' => 'BNBUSDT',
                'side' => 'BUY',
                'quantity' => 0.02,
                'type' => 'LIMIT',
                'price' => 305,
                'timeInForce' => 'GTC',
                'positionSide' => 'LONG'
            ],
            true
        );
    }

    public function createFuturesCloseOrder()
    {
        return $this->httpRequest(
            'fapi/v1/order',
            'POST',
            [
                'fapi' => true,
                'symbol' => 'APTUSDT',
                'side' => 'SELL',
                'quantity' => 0.6,
                'type' => 'LIMIT',
                'price' => 8.6,
                'timeInForce' => 'GTC',
                'positionSide' => 'LONG'
            ],
            true
        );
    }
}