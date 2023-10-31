<?php

namespace App\Trading;

use App\Binance\Binance;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class TradingManager
{
    private static ?Binance $binance = null;

    /**
     * @throws Exception
     */
    public static function handleDown(): void
    {
        if (self::binance()->hasShortPosition()) {
            self::maybeTakeShortProfit();
        }

        if(self::shouldOpenLong()) {
            self::openLong();
        }
    }

    /**
     * @throws Exception
     */
    public static function handleUp(): void
    {
        if (self::binance()->hasLongPosition()) {
            self::maybeTakeLongProfit();
        }

        if(self::shouldOpenShort()) {
            self::openShort();
        }
    }

    /**
     * @throws Exception
     */
    public static function import(): void
    {
        $orders = self::binance()->orders();

        foreach ($orders as $order) {
            $order['cumQty'] = $order['executedQty'];
            self::upsertOrder($order);
            self::collectTrades($order['orderId']);
        }
    }

    /**
     * @throws Exception
     */
    public static function importTrades()
    {
        $orders = Order::all()->pluck('order_id');

        $orders->each(function($orderId) {
            self::collectTrades($orderId);
        });
    }

    /**
     * @throws Exception
     */
    public static function importOrders(): void
    {
        $orders = self::binance()->orders();

        foreach ($orders as $order) {
            $order['cumQty'] = $order['executedQty'];
            self::upsertOrder($order);
        }
    }

    /**
     * @throws Exception
     */
    public static function positions()
    {
        dd(self::binance()->positions());
    }

    /**
     * @throws Exception
     */
    private static function openLong(): void
    {
        $binanceOrder = self::binance()->openLong(
            self::minSize(),
            self::currentPrice() - 0.1,
        );

        self::upsertOrder($binanceOrder);
    }

    /**
     * @throws Exception
     */
    private static function openShort(): void
    {
        $binanceOrder = self::binance()->openShort(
            self::minSize(),
            self::currentPrice() + 0.1,
        );

        self::upsertOrder($binanceOrder);
    }

    private static function minSize(): float
    {
        return round(6 / self::currentPrice(), 2);
    }

    private static function currentPrice(): float
    {
        return round(self::binance()->getMarkPrice(), 2);
    }

    private static function binance(): ?Binance
    {
        if (!self::$binance) {
            self::$binance = new Binance();
        }

        return self::$binance;
    }

    private static function upsertOrder(array $data): void
    {
        Order::query()->upsert([
            [
                'order_id' => $data['orderId'],
                'symbol' => $data['symbol'],
                'status' => Order::STATUS[$data['status']],
                'client_order_id' => $data['clientOrderId'],
                'price' => $data['price'],
                'avg_price' => $data['avgPrice'],
                'orig_qty' => $data['origQty'],
                'executed_qty' => $data['executedQty'],
                'cum_qty' => $data['cumQty'],
                'cum_quote' => $data['cumQuote'],
                'time_in_force' => $data['timeInForce'],
                'type' => $data['type'],
                'reduce_only' => $data['reduceOnly'],
                'close_position' => $data['closePosition'],
                'side' => $data['side'],
                'position_side' => $data['positionSide'],
                'stop_price' => $data['stopPrice'],
                'working_type' => $data['workingType'],
                'price_protect' => $data['priceProtect'],
                'orig_type' => $data['origType'],
                'price_match' => $data['priceMatch'],
                'self_trade_prevention_mode' => $data['selfTradePreventionMode'],
                'good_till_date' => $data['goodTillDate'],
                'update_time' => $data['updateTime'],
            ]
        ],
            ['id'],
            ['status', 'avg_price', 'cum_qty', 'cum_quote', 'executed_qty', 'update_time']
        );
    }

    public static function test(): void
    {
        dd(Order::query()
            ->where('status', '=', Order::STATUS_FILLED)
            ->where('position_side', '=', Order::POSITION_SIDE_SHORT)
            ->where('avg_price', '>=', self::currentPrice())
            ->orderByDesc('update_time')
            ->first());
    }

    /**
     * @throws Exception
     */
    private static function collectTrades(mixed $orderId): void
    {
        $binanceTrades = self::binance()->collectTrades(
            $orderId
        );

        foreach ($binanceTrades as $binanceTrade) {
            self::upsertTrade($binanceTrade);
        }
    }

    private static function upsertTrade(array $data): void
    {
        Trade::query()->upsert([
            [
                'id' => $data['id'],
                'symbol' => $data['symbol'],
                'order_id' => $data['orderId'],
                'side' => $data['side'],
                'price' => $data['price'],
                'qty' => $data['qty'],
                'realized_pnl' => $data['realizedPnl'],
                'margin_asset' => $data['marginAsset'],
                'quote_qty' => $data['quoteQty'],
                'commission' => $data['commission'],
                'commission_asset' => $data['commissionAsset'],
                'time' => $data['time'],
                'position_side' => $data['positionSide'],
                'maker' => $data['maker'],
                'buyer' => $data['buyer'],
            ]
        ],
            ['id'],
            ['time']
        );
    }

    public static function status()
    {
        $orders = Order::query()
            ->where('status', '=', Order::STATUS_FILLED)
            ->get();

        $longs = $orders->where('position_side', '=', Order::POSITION_SIDE_LONG);
        $shorts = $orders->where('position_side', '=', Order::POSITION_SIDE_SHORT);

        dd([
            'long' => $longs->map(function ($order) {
                $order->cum_qty = $order->reduce_only ? $order->cum_qty * -1 : $order->cum_qty;
                return $order;
            })->sum('cum_qty'),
            'short' => $shorts->map(function ($order) {
                $order->cum_qty = $order->reduce_only ? $order->cum_qty * -1 : $order->cum_qty;
                return $order;
            })->sum('cum_qty'),
        ]);
    }

    /**
     * @throws Exception
     */
    private static function maybeTakeLongProfit(): void
    {
        if (self::binance()->hasLongProfit()) {
            self::takeLongProfit();
        }
    }

    /**
     * @throws Exception
     */
    private static function maybeTakeShortProfit(): void
    {
        if (self::binance()->hasShortProfit()) {
            self::takeShortProfit();
        }
    }

    /**
     * @throws Exception
     */
    private static function takeLongProfit(): void
    {
        tap(self::getClosableLongOrder(), function ($order) {
            $binanceOrder = self::binance()->closeLong(
                $order->orig_qty,
                self::currentPrice() + 0.1,
            );

            self::upsertOrder($binanceOrder);
        });
    }

    /**
     * @throws Exception
     */
    private static function takeShortProfit(): void
    {
        tap(self::getClosableShortOrder(), function ($order) {
            $binanceOrder = self::binance()->closeShort(
                $order->orig_qty,
                self::currentPrice() - 0.1,
            );

            self::upsertOrder($binanceOrder);
        });
    }

    private static function getClosableLongOrder(): ?object
    {
        return Order::query()
            ->where('status', '=', Order::STATUS_FILLED)
            ->where('position_side', '=', Order::POSITION_SIDE_LONG)
            ->where('avg_price', '<=', self::currentPrice())
            ->orderByDesc('update_time')
            ->first();
    }

    private static function getClosableShortOrder(): ?object
    {
        return Order::query()
            ->where('status', '=', Order::STATUS_FILLED)
            ->where('position_side', '=', Order::POSITION_SIDE_SHORT)
            ->where('avg_price', '>=', self::currentPrice())
            ->orderByDesc('update_time')
            ->first();
    }

    /** @noinspection DuplicatedCode */
    private static function shouldOpenShort(): bool
    {
        // Retrieve latest short order in last 2 hours
        $noShortOrderFilledLastHour = null === Order::query()
            ->where('status', '=', Order::STATUS_FILLED)
            ->where('position_side', '=', Order::POSITION_SIDE_SHORT)
            ->where('side', '=', Order::SIDE_SELL)
            ->where('update_time', '>=', self::last2Hours())
            ->orderByDesc('update_time')
            ->first();

        // Retrieve latest short order with status NEW
        $noPendingShortOrder = null === Order::query()
            ->where('status', '=', Order::STATUS_NEW)
            ->where('position_side', '=', Order::POSITION_SIDE_SHORT)
            ->where('side', '=', Order::SIDE_SELL)
            ->orderByDesc('update_time')
            ->first();

        return $noShortOrderFilledLastHour && $noPendingShortOrder;
    }

    /** @noinspection DuplicatedCode */
    private static function shouldOpenLong(): bool
    {
        // Retrieve latest long order in last 2 hours
        $noLongOrderFilledLastHour = null === Order::query()
                ->where('status', '=', Order::STATUS_FILLED)
                ->where('position_side', '=', Order::POSITION_SIDE_LONG)
                ->where('side', '=', Order::SIDE_BUY)
                ->where('update_time', '>=', self::last2Hours())
                ->orderByDesc('update_time')
                ->first();

        // Retrieve latest long order with status NEW
        $noPendingLongOrder = null === Order::query()
                ->where('status', '=', Order::STATUS_NEW)
                ->where('position_side', '=', Order::POSITION_SIDE_LONG)
                ->where('side', '=', Order::SIDE_BUY)
                ->orderByDesc('update_time')
                ->first();

        return $noLongOrderFilledLastHour && $noPendingLongOrder;
    }

    private static function last2Hours(): int
    {
        return Carbon::now()->subHours(2)->timestamp * 1000;
    }

    /**
     * @throws Exception
     */
    public static function collectProfits(): void
    {
        self::importTrades();

        $trades = Trade::query()
            ->where('realized_pnl', '>', 0)
            ->get();

        if ($trades->count() > 0) {
            $trades->each(function ($trade) {
                $fee = 'BNB' === $trade->commission_asset ? $trade->commission * $trade->price : $trade->commission;

                $profit = [
                    'trade_id' => $trade->id,
                    'realized_profit' => $trade->realized_pnl,
                    'fee' => $fee,
                    'net_profit' => $trade->realized_pnl - $fee,
                    'roe' => $trade->realized_pnl / $trade->quote_qty / 4 * 100,
                    'created_at' => Carbon::createFromTimestampMs($trade->time)->toDateTimeString(),
                ];

                self::upsertProfit($profit);
            });
        }
    }

    private static function upsertProfit(array $data): void
    {
        Profit::query()->upsert(
            $data,
            ['trade_id'],
            ['realized_profit', 'fee', 'net_profit', 'roe', 'created_at']
        );
    }
}