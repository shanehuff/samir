<?php

namespace Modules\ThreeCommas\Client;

use Illuminate\Support\Facades\Http;
use Modules\ThreeCommas\Contracts\ThreeCommasClientContract;
use Modules\ThreeCommas\Interactions\InteractWith3CommasReponse;

class ThreeCommasClient implements ThreeCommasClientContract
{
    use InteractWith3CommasReponse;

    private string $baseURI;

    /**
     * @inheritdoc
     */
    public function setBaseURI(string $uri): ThreeCommasClientContract
    {
        $this->baseURI = $uri;
        return $this;
    }

    /**
     * Test connectivity to the Rest API
     * @see https://github.com/3commas-io/3commas-official-api-docs#test-connectivity-to-the-rest-api-permission-none-security-none
     */
    public function ping(): mixed
    {
        try {
            return $this->decodeResponseJson(Http::get($this->baseURI . '/ver1/ping'));
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}