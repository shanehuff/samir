<?php

namespace App\Binance;

use Binance\API;

class ApiClient extends API
{
    /**
     * @throws \Exception
     */
    public function saving(): ?array
    {
        return $this->httpRequest('v1/lending/union/account', 'GET', [ 'sapi' => true ], true);
    }
}