<?php

namespace App\Binance;

use Exception;
use Illuminate\Support\Collection;

class Binance
{
    protected ?FuturesClient $client;

    protected Collection $positions;

    protected ?array $long;

    protected ?array $short;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->client = new FuturesClient(
            config('services.binance.key'),
            config('services.binance.secret')
        );

        $this->positions = $this->client->positions();
        $this->long = $this->positions->get(0);
        $this->short = $this->positions->get(1);

        return $this;
    }

    /**
     * @throws Exception
     */
    public function openLong(float $size, float $entry): array
    {
        return $this->client->openLong($size, $entry);
    }

    /**
     * @throws Exception
     */
    public function openShort(float $size, float $entry): array
    {
        return $this->client->openShort($size, $entry);
    }

    public function getMarkPrice()
    {
        return $this->long['markPrice'];
    }

    /**
     * @throws Exception
     */
    public function collectTrades($orderId = null, $startTime = null): array
    {
        return $this->client->userTrades($orderId, $startTime);
    }
}