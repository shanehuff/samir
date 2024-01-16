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

    protected ?string $symbol = null;

    /**
     * @throws Exception
     */
    public function __construct($symbol = null)
    {
        $this->client = new FuturesClient(
            config('services.binance.key'),
            config('services.binance.secret')
        );
        $this->client->useSymbol($symbol);
        $this->symbol = $symbol;
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
    public function collectTrades($startTime = null): array
    {
        return $this->client->userTrades($startTime);
    }

    /**
     * @throws Exception
     */
    public function orders(string $symbol = null, ?string $updateTime = null): array
    {
        return $this->client->allOrders($symbol?:$this->symbol, $updateTime);
    }

    public function positions(): Collection
    {
        return $this->positions;
    }

    public function hasLongPosition(): bool
    {
        return $this->symbol === $this->long['symbol'] && $this->long['positionAmt'] > 0;
    }

    public function hasShortPosition(): bool
    {
        return $this->symbol === $this->short['symbol'] && abs($this->short['positionAmt']) > 0;
    }

    public function hasLongProfit(): bool
    {
        return $this->symbol === $this->long['symbol'] && $this->long['unRealizedProfit'] > 0;
    }

    public function hasShortProfit(): bool
    {
        return $this->symbol === $this->short['symbol'] && $this->short['unRealizedProfit'] > 0;
    }

    /**
     * @throws Exception
     */
    public function closeLong(float $size, float $entry): array
    {
        return $this->client->closeLong($size, $entry);
    }

    /**
     * @throws Exception
     */
    public function closeShort(float $size, float $entry): array
    {
        return $this->client->closeShort($size, $entry);
    }

    /**
     * @throws Exception
     */
    public function income($time = null): array
    {
        return $this->client->income($time);
    }
}