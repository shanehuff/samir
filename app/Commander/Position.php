<?php

namespace App\Commander;

use App\Models\Commander;
use App\Models\CommanderTrade;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Position
{
    protected Commander $commander;

    protected ?float $buy_size = null;

    protected ?float $buy_entry = null;

    protected ?float $sell_size = null;

    protected ?float $sell_entry = null;

    protected Carbon $from_date;

    protected Carbon $to_date;

    /**
     * @return Commander
     */
    public function getCommander(): Commander
    {
        return $this->commander;
    }

    /**
     * @param Commander $commander
     */
    public function setCommander(Commander $commander): static
    {
        $this->commander = $commander;

        return $this;
    }

    public function calculate(): static
    {
        $trades = $this->commander->getSummaryTrades();

        $this->createFromTrades($trades);

        return $this;
    }

    public function createFromTrades(Collection $trades): static
    {
        $buy = $trades->firstWhere('side', 'buy');
        $sell = $trades->firstwhere('side', 'sell');

        if ($buy) {
            $this->buy_entry = $buy->entry;
            $this->buy_size = $buy->size;
            $this->from_date = $this->resolveFromDateFromTrades($trades);
        }

        if ($sell) {
            $this->sell_entry = $sell->entry;
            $this->sell_size = $sell->size;
            $this->to_date = $this->resolveToDateFromTrades($trades);
        }

        return $this;
    }

    /**
     * @return float
     */
    public function getBuySize(): float
    {
        return $this->buy_size;
    }

    /**
     * @param float $buy_size
     */
    public function setBuySize(float $buy_size): void
    {
        $this->buy_size = $buy_size;
    }

    /**
     * @return float
     */
    public function getBuyEntry(): float
    {
        return $this->buy_entry;
    }

    /**
     * @param float $buy_entry
     */
    public function setBuyEntry(float $buy_entry): void
    {
        $this->buy_entry = $buy_entry;
    }

    /**
     * @return float
     */
    public function getSellSize(): float
    {
        return $this->sell_size;
    }

    /**
     * @param float $sell_size
     */
    public function setSellSize(float $sell_size): void
    {
        $this->sell_size = $sell_size;
    }

    /**
     * @return float
     */
    public function getSellEntry(): float
    {
        return $this->sell_entry;
    }

    /**
     * @param float $sell_entry
     */
    public function setSellEntry(float $sell_entry): void
    {
        $this->sell_entry = $sell_entry;
    }

    public function getDuration(): int
    {
        return $this->to_date->diffInDays($this->from_date);
    }

    /**
     * @return string
     */
    public function getFromDate(): string
    {
        return $this->from_date;
    }

    /**
     * @param string $from_date
     */
    public function setFromDate(string $from_date): void
    {
        $this->from_date = new Carbon($from_date);
    }

    /**
     * @return string
     */
    public function getToDate(): string
    {
        return $this->to_date;
    }

    /**
     * @param string $to_date
     */
    public function setToDate(string $to_date): void
    {
        $this->to_date = new Carbon($to_date);
    }

    public function resolveFromDateFromTrades(Collection $trades): Carbon
    {
        return new Carbon($trades->min('from_date'));
    }

    public function resolveToDateFromTrades(Collection $trades): Carbon
    {
        return new Carbon($trades->max('to_date'));
    }

    public function hasRealizedProfit(): bool
    {
        return $this->sell_size && $this->buy_size;
    }

    public function getBuySizeInSymbol(): float|int
    {
        return $this->getBuySize() / $this->getBuyEntry();
    }
}
