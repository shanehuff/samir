<?php

namespace App\Trading;

use Exception;
use Illuminate\Database\Eloquent\Collection;

class ChampionManager
{
    public function list()
    {
        return Champion::query()->get();
    }

    /**
     * @throws Exception
     */
    public function sync(Champion $champion): void
    {
        TradingManager::useChampion($champion);
        TradingManager::importRecentOrders();

        $result = $champion->orders->reduce(function ($carry, $item) {
            $cumQuote = $item->reduce_only ? -$item->cum_quote : $item->cum_quote;
            $carry[$item->position_side] = ($carry[$item->position_side] ?? 0) + $cumQuote;
            $carry['PROFIT'] = ($carry['PROFIT'] ?? 0) + $item->trades->sum('realized_pnl');

            return $carry;
        }, []);

        $result['LONG'] = $result['LONG'] ?? 0;
        $result['SHORT'] = $result['SHORT'] ?? 0;
        $result['PROFIT'] = $result['PROFIT'] ?? 0;

        $champion->update([
            'onduty' => ($result['LONG'] + $result['SHORT']) / 2,
            'profit' => $result['PROFIT'],
            'roi' => $result['PROFIT'] / $champion->capital
        ]);
    }

    public function getActiveFarmers(): Collection|array
    {
        return Champion::query()
            ->where('archetype', 'farmer')
            ->where('status', 'active')
            ->get();
    }
}