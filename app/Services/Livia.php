<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\MultipartStream;

class Livia
{
    protected ?Client $guzzleClient = null;

    protected string $baseUrl = 'http://livia.5oaqkapv5e-e9249vmxw3kr.p.temp-site.link/';

    public function __construct()
    {

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
                'Accept' => 'application/json'
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

    public function sendPrompt($payloadValue) {
        // Define the multipart form data with only the 'payload' field
        $multipartData = [
            [
                'name'     => 'payload',
                'contents' => $payloadValue,
            ],
        ];

        // Create a MultipartStream
        $multipartStream = new MultipartStream($multipartData);

        // Send the POST request with multipart-form data
        $response = $this->sendRequest('POST', '/api/chat-gpt-prompt', [
            'headers' => [
                'Content-Type' => 'multipart/form-data; boundary=' . $multipartStream->getBoundary(),
            ],
            'body'    => $multipartStream,
        ]);

        info($response->getBody());
    }

    public function sendUpscale()
    {
        $response = $this->sendRequest('PUT', '/api/prompt-status');

        info($response->getBody());
    }
}