<?php

namespace App\Trading;

use Exception;

class ChampionManager
{
    public function list()
    {
        return Champion::query()->get();
    }

    /**
     * @throws Exception
     */
    public function sync(int $championId): void
    {
        /** @var Champion $champion */
        $champion = Champion::query()->find($championId);

        TradingManager::useChampion($champion);
        TradingManager::importRecentOrders();

        if ($champion) {
            $result = $champion->orders->reduce(function ($carry, $item) {
                $cumQuote = $item->reduce_only ? -$item->cum_quote : $item->cum_quote;
                $carry[$item->position_side] = ($carry[$item->position_side] ?? 0) + $cumQuote;
                $carry['PROFIT'] = ($carry['PROFIT'] ?? 0) + $item->trades->sum('realized_pnl');

                return $carry;
            }, []);

            $champion->update([
                'onduty' => ($result['LONG'] + $result['SHORT']) / 2,
                'profit' => $result['PROFIT'],
                'roi' => $result['PROFIT'] / $champion->capital
            ]);
        }
    }
}