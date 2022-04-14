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
     * 
     */
    protected function signed(string $signature)
    {
        return Http::withHeaders([
            'APIKEY' => config('commas.api_key'),
            'Signature' => $signature
        ]);
    }

    /**
     * Generate the signature for endpoint security
     * 
     * @param string $url
     * @param string $value Query string of request
     * @return string
     */
    protected function generateSignature(string $url, string $value): string
    {
        $string = $value ? "/public/api{$url}?{$value}" : "/public/api{$url}";
        return hash_hmac('sha256', $string, config('commas.secret_key'));
    }

    /**
     * Build a query string from array of parameters
     * 
     * @param array $data
     * @return string
     */
    protected function buildQuery(array $data): string
    {
        return http_build_query($data);
    }

    /**
     * Test connectivity to the Rest API
     * @see https://github.com/3commas-io/3commas-official-api-docs#test-connectivity-to-the-rest-api-permission-none-security-none
     * 
     * @return mixd
     * @throws \Exception
     */
    public function ping(): mixed
    {
        try {
            return $this->decodeResponseJson(Http::get($this->baseURI . '/ver1/ping'));
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Get a bot information
     * 
     * @see https://github.com/3commas-io/3commas-official-api-docs/blob/master/bots_api.md#bot-info-permission-bots_read-security-signed
     * @return array
     * @throws \Exception
     */
    public function botInfo(int $botId): array
    {
        try {
            $url = '/ver1/bots/' . $botId . '/show';

            $params = [
                // 'include_events' => true
            ];

            $signature = $this->generateSignature($url, $this->buildQuery($params));

            return $this->decodeResponseJson(
                $this->signed($signature)->get($this->baseURI . $url)
            );
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Get list of bot
     * 
     * @see https://github.com/3commas-io/3commas-official-api-docs/blob/master/bots_api.md#user-bots-permission-bots_read-security-signed
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function bots(array $params): array
    {
        try {
            $url = '/ver1/bots';

            $signature = $this->generateSignature($url, $this->buildQuery($params));

            return $this->decodeResponseJson(
                $this->signed($signature)->get($this->baseURI . $url)
            );
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}