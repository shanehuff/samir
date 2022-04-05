<?php

namespace App\Commander;

use App\Models\Commander;
use App\Models\CommanderTrade;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Profit
{
    protected Commander $commander;

    protected float $total;

    protected float $buy_size;

    protected float $buy_entry;

    protected float $sell_size;

    protected float $sell_entry;

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
        $trades = DB::table(app(CommanderTrade::class)->getTable())
            ->select(
                'side',
                DB::raw('SUM(amount) as size'),
                DB::raw('AVG(entry) as entry')
            )
            ->where('commander_id', $this->commander->id)
            ->whereNotNull('amount')
            ->groupBy('side')
            ->get();

        $this->createFromTrades($trades);

        return $this;
    }

    /**
     * @return float
     */
    public function getTotal(): float
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

    public function createFromTrades(Collection $trades): static
    {
        $buy = $trades->firstWhere('side', 'buy');
        $sell = $trades->firstwhere('side', 'sell');
        $this->buy_entry = $buy->entry;
        $this->buy_size = $buy->size;
        $this->sell_entry = $sell->entry;
        $this->sell_size = $sell->size;

        if ($buy && $sell) {
            $this->total = ($sell->entry - $buy->entry) * ($buy->size / $buy->entry);
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
}
