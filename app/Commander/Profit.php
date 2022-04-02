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

        $total = $this->resolveTotalFromTrades($trades);

        if ($total) {
            $this->setTotal($total);
        }

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

    public function resolveTotalFromTrades(Collection $trades): float|bool|int
    {
        $buy = $trades->firstWhere('side', 'buy');
        $sell = $trades->firstwhere('side', 'sell');

        if ($buy && $sell) {
            return ($sell->entry - $buy->entry) * ($buy->size / $buy->entry);
        }

        info(
            sprintf('ResolveTotalFromTrades: Trade data are invalid: %s', json_encode($trades->toArray()))
        );

        return false;
    }
}
