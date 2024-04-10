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

        $result = $champion->filledOrders->reduce(function ($carry, $item) {
            $cumQuote = $item->reduce_only ? -$item->cum_quote : $item->cum_quote;
            $carry[$item->position_side] = ($carry[$item->position_side] ?? 0) + $cumQuote;
            $carry['PROFIT'] = ($carry['PROFIT'] ?? 0) + $item->trades->sum('realized_pnl');
            $carry['FEE'] = ($carry['FEE'] ?? 0) + $item->trades->sum('commission');

            return $carry;
        }, []);

        $result['LONG'] = ($result['LONG'] ?? 0) < 0 ? 0 : ($result['LONG'] ?? 0);
        $result['SHORT'] = ($result['SHORT'] ?? 0) < 0 ? 0 : ($result['SHORT'] ?? 0);
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
            $tokenCommissionTrades = $order->trades->where('is_buyer', '=', true);
            $usdtCommissionTrades = $order->trades->where('is_buyer', '=', false);

            //$carry['FEE'] = ($carry['FEE'] ?? 0) + ($tokenCommissionTrades->sum('commission') * $tokenCommissionTrades->avg('price'));
            //$carry['ONDUTY'] = ($carry['ONDUTY'] ?? 0) + ('BUY' === $order->side ? $order->cummulative_quote_qty : -$order->cummulative_quote_qty);
            $carry['QTY'] = ($carry['QTY'] ?? 0) + ('BUY' === $order->side ? $order->executed_qty : -$order->executed_qty);

            $carry['SOLD_QUOTE'] = ($carry['SOLD_QUOTE'] ?? 0) + $usdtCommissionTrades->sum('quote_qty');
            $carry['SOLD_QTY'] = ($carry['SOLD_QTY'] ?? 0) + $usdtCommissionTrades->sum('qty');
            $carry['SOLD_FEE'] = ($carry['SOLD_FEE'] ?? 0) + $usdtCommissionTrades->sum('commission');

            $carry['BOUGHT_QUOTE'] = ($carry['BOUGHT_QUOTE'] ?? 0) + $tokenCommissionTrades->sum('quote_qty');
            $carry['BOUGHT_QTY'] = ($carry['BOUGHT_QTY'] ?? 0) + $tokenCommissionTrades->sum('qty');
            $carry['BOUGHT_FEE'] = ($carry['BOUGHT_FEE'] ?? 0) + $tokenCommissionTrades->sum('commission') * $tokenCommissionTrades->avg('price');

            return $carry;
        }, []);

        $result['AVG_BUY_PRICE'] = ($result['BOUGHT_QUOTE'] - $result['BOUGHT_FEE']) / $result['BOUGHT_QTY'];

        if($result['SOLD_QTY'] > 0) {
            $result['AVG_SELL_PRICE'] = ($result['SOLD_QUOTE'] - $result['SOLD_FEE']) / $result['SOLD_QTY'];
            $profit = ($result['AVG_SELL_PRICE'] - $result['AVG_BUY_PRICE']) * $result['SOLD_QTY'];
        } else {
            $result['AVG_SELL_PRICE'] = 0;
            $profit = 0;
        }
        
        $result['FEE'] = $result['BOUGHT_FEE'] + $result['SOLD_FEE'];
        $result['ONDUTY'] = $result['QTY'] * $result['AVG_BUY_PRICE'];

        $champion->update([
            'onduty' => $result['ONDUTY'],
            'profit' => $profit,
            'roi' => $profit / $champion->capital,
            'fee' => $result['FEE'],
            'income' => 0,
            'entry' => $result['AVG_BUY_PRICE']
        ]);
    }
}
