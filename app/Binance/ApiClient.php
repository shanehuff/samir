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
}