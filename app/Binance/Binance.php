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

    /**
     * @throws Exception
     */
    public function orders(): array
    {
        return $this->client->allOrders();
    }

    public function positions(): Collection
    {
        return $this->positions;
    }

    public function hasLongPosition(): bool
    {
        return $this->long['positionAmt'] > 0;
    }

    public function hasShortPosition(): bool
    {
        return abs($this->short['positionAmt']) > 0;
    }

    public function hasLongProfit(): bool
    {
        return $this->long['unRealizedProfit'] > 0;
    }

    public function hasShortProfit(): bool
    {
        return $this->short['unRealizedProfit'] > 0;
    }

    /**
     * @throws Exception
     */
    public function closeLong(float $size, float $entry): array
    {
        return $this->client->closeLong($size, $entry);
    }

    public function closeShort(float $size, float $entry)
    {
        return $this->client->closeShort($size, $entry);
    }
}