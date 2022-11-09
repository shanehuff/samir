<?php

namespace App\Binance;

use Binance\API;

class FuturesClient extends API
{
    public function account()
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

    public function balance()
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

    public function userTrades()
    {
        return $this->httpRequest(
            'fapi/v1/userTrades',
            'GET',
            [
                'fapi' => true,
                'symbol' => 'BNBBUSD',
            ],
            true
        );
    }

    public function positions()
    {
        return $this->httpRequest(
            'fapi/v2/positionRisk',
            'GET',
            [
                'fapi' => true,
                'symbol' => 'BNBBUSD'
            ],
            true
        );
    }
}