<?php

namespace App\Commander;

use App\Models\Commander;
use App\Models\CommanderTrade;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Profit
{
    protected Commander $commander;

    protected ?float $total = null;

    protected float $daily_profit_percentage;

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
        $position = $this->commander->getPosition();

        $this->createFromPosition($position);

        return $this;
    }

    /**
     * @return float|null
     */
    public function getTotal(): ?float
    {
        return $this->total;
    }

    /**
     * @param float $total
     */
    public function setTotal(float $total): void
    {
        $this->total = $total;
    }

    public function calculateDailyProfit()
    {
        $position = $this->commander->getPosition();

        $this->daily_profit_percentage = $this->total / $this->commander->fund * 100 / $position->getDuration();
    }

    /**
     * @return float
     */
    public function getDailyProfitPercentage(): float
    {
        return $this->daily_profit_percentage;
    }

    public function getMonthlyProfitPercentage(): float|int
    {
        return $this->daily_profit_percentage * 30;
    }

    public function getApy(): float|int
    {
        return $this->daily_profit_percentage * 365;
    }

    public function createFromPosition(Position $position)
    {
        if($position->hasRealizedProfit()) {
            $this->total = ($position->getSellEntry() - $position->getBuyEntry()) * ($position->getBuySize() / $position->getBuyEntry());
            $this->calculateDailyProfit();
        }
    }
}
