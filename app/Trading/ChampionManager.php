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
            $carry['FEE'] = ($carry['FEE'] ?? 0) + $item->trades->sum('commission');

            return $carry;
        }, []);

        $result['LONG'] = $result['LONG'] ?? 0;
        $result['SHORT'] = $result['SHORT'] ?? 0;
        $result['PROFIT'] = $result['PROFIT'] ?? 0;
        $result['FEE'] = $result['FEE'] ?? 0;

        $champion->update([
            'onduty' => ($result['LONG'] + $result['SHORT']) / 2,
            'profit' => $result['PROFIT'],
            'roi' => $result['PROFIT'] / $champion->capital,
            'fee' => $result['FEE'],
            'income' => $champion->funding_income
        ]);
    }

    public function getActiveFarmers(): Collection|array
    {
        return Champion::query()
            ->where('archetype', 'farmer')
            ->where('status', 'active')
            ->get();
    }

    public function getActiveLootcycles(): Collection|array
    {
        return Champion::query()
            ->where('archetype', 'lootcycle')
            ->where('status', 'active')
            ->get();
    }

    /**
     * @throws Exception
     */
    public function syncLootcycle(Champion $champion): void
    {
        $orders = SpotOrder::query()
            ->where('champion_id', '=', $champion->id)
            ->where('status', '=', SpotOrder::STATUS_FILLED)
            ->get();

        $result = $orders->reduce(function ($carry, $order) {
            $carry['FEE'] = ($carry['FEE'] ?? 0) + ($order->trades->sum('commission') * $order->trades->avg('price'));
            $carry['ONDUTY'] = ($carry['ONDUTY'] ?? 0) + $order->cummulative_quote_qty;
            $carry['QTY'] = ($carry['QTY'] ?? 0) + $order->executed_qty;

            return $carry;
        }, []);

        $champion->update([
            'onduty' => $result['ONDUTY'],
            'profit' => 0,
            'roi' => 0,
            'fee' => $result['FEE'],
            'income' => 0,
            'entry' => ($result['ONDUTY'] - $result['FEE']) / $result['QTY']
        ]);
    }
}