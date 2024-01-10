<?php

namespace App\Trading;

use Carbon\Carbon;

class ProfitManager
{

    public static function makeFromTrade($trade): void
    {
        $fee = self::getFeeFromTrade($trade);
        $buy = $trade->side === Order::SIDE_BUY ? $trade : $trade->counterTrade();
        $sell = $trade->side === Order::SIDE_SELL ? $trade : $trade->counterTrade();
        $netProfit = $trade->realized_pnl - $fee;

        $profit = [
            'trade_id' => $trade->id,
            'realized_profit' => $trade->realized_pnl,
            'fee' => $fee,
            'net_profit' => $netProfit,
            'roe' => $netProfit / $buy->quote_qty * 100,
            'created_at' => Carbon::createFromTimestampMs($trade->time)->toDateTimeString(),
            'duration_readable' => self::getReadableDuration($buy->time, $sell->time),
            'duration' => abs($buy->time - $sell->time),
            'buy_price' => $buy->price,
            'sell_price' => $sell->price,
            'symbol' => $sell->symbol
        ];

        self::upsertProfit($profit);
    }

    private static function upsertProfit(array $data): void
    {
        Profit::query()->upsert(
            $data,
            ['trade_id'],
            ['realized_profit', 'fee', 'net_profit', 'roe', 'created_at', 'duration', 'buy_price', 'sell_price', 'duration_readable', 'symbol']
        );
    }

    private static function getFeeFromTrade($trade): float
    {
        $fee = 'BNB' === $trade->commission_asset ? $trade->commission * $trade->price : $trade->commission;
        $counterTradeFee = 'BNB' === $trade->counterTrade()->commission_asset ? $trade->counterTrade()->commission * $trade->counterTrade()->price : $trade->counterTrade()->commission;

        return $fee + $counterTradeFee;
    }

    private static function getReadableDuration(mixed $time_in, mixed $time_out): string
    {
        $duration = number_format(abs($time_in - $time_out) / 1000 / 60 / 60, 2);

        $h = floor($duration);
        $m = floor(($duration - $h) * 60);

        // convert $duration to readable format like 1h 30m
        $duration = $h . 'h ' . $m . 'm';

        // if duration is less than 1 hour, then return only minutes
        if ((int)$h === 0) {
            $duration = $m . 'm';
        }

        return $duration;
    }
}