<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;

class Keisha
{
    protected ?Client $guzzleClient = null;

    protected string $baseUrl = 'https://keisha.chillbits.com/';

    protected string $token = '';

    public function __construct()
    {
        $this->token = config('services.keisha.key');
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @throws GuzzleException
     */
    public function sendRequest(string $method, string $endpoint, $requestOptions = []): ResponseInterface
    {
        return $this->getHttpClient()->request(
            $method,
            $endpoint,
            $requestOptions
        );
    }

    protected function getHttpClient(): Client
    {
        if (!$this->guzzleClient) {
            $this->guzzleClient = $this->createGuzzleClient(
                $this->getDefaultRequestOptions()
            );
        }

        return $this->guzzleClient;
    }

    protected function getDefaultRequestOptions(): array
    {
        return [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token
            ]
        ];
    }

    protected function createGuzzleClient($options): Client
    {
        return new Client(
            array_merge(
                [
                    'base_uri' => $this->getBaseUrl(),
                    'timeout' => 30,
                    'cookies' => false,
                    'allow_redirects' => true,
                    'http_errors' => true,
                ],
                $options
            )
        );
    }

    /**
     * @throws GuzzleException
     */
    public function getPricing(): Collection
    {
        $response = $this->sendRequest('GET', '/api/pricing');
        return collect(json_decode((string)$response->getBody(), true));
    }

    /**
     * @throws GuzzleException
     */
    public function getStepnPricing(): Collection
    {
        $response = $this->sendRequest('GET', '/api/stepn/pricing');
        return collect(json_decode((string)$response->getBody(), true));
    }

    public function loginStepn(string $code): Collection
    {
        $response = $this->sendRequest(
            'POST',
            '/api/stepn/login',
            [
                'json' => [
                    'code' => $code
                ]
            ]
        );

        return collect(json_decode((string)$response->getBody(), true));
    }
}