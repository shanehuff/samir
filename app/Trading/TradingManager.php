<?php

namespace App\Trading;

use App\Binance\Binance;
use Carbon\Carbon;
use Exception;

class TradingManager
{
    private static ?Binance $binance = null;

    private static ?Champion $champion = null;

    /**
     * @throws Exception
     */
    public static function handleDown(): void
    {
        info('Use champion ID: ' . self::$champion->id);
        info('Handle Down');
        if (self::binance()->hasShortPosition()) {
            info('Has short position');
            self::maybeTakeShortProfit();
        }

        if (self::shouldOpenLong()) {
            info('Should open long');
            self::openLong();
        }
        info('Handle Down End');
    }

    /**
     * @throws Exception
     */
    public static function handleUp(): void
    {
        info('Handle Up');

        info('Use champion ID: ' . self::$champion->id);

        if (self::binance()->hasLongPosition()) {
            info('Has long position');
            self::maybeTakeLongProfit();
        }

        if (self::shouldOpenShort()) {
            info('Should open short');
            self::openShort();
        }

        info('Handle Up End');
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
    public static function importTrades(): void
    {
        $orders = Order::all()->pluck('order_id');

        $orders->each(function ($orderId) {
            self::collectTrades($orderId);
        });
    }

    /**
     * @throws Exception
     */
    public static function importRecentTrades(): void
    {
        tap(Trade::query()
            ->orderByDesc('time')
            ->first(),
            function ($latestTrade) {
                self::collectTrades($latestTrade->time);
            }
        );
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
    public static function importRecentOrders(): void
    {
        tap(
            Order::query()
                ->where('status', '=', Order::STATUS_NEW)
                ->where('champion_id', '=', self::$champion->id)
                ->orderBy('update_time')
                ->first(),

            function ($latestOrder) {
                if ($latestOrder) {
                    $orders = self::binance()->orders(self::$champion->symbol, $latestOrder->update_time);

                    foreach ($orders as $order) {
                        $order['cumQty'] = $order['executedQty'];
                        self::upsertOrder($order);
                    }
                }
            }
        );
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
            self::currentPrice() - self::priceGap(),
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
            self::currentPrice() + self::priceGap(),
        );

        self::upsertOrder($binanceOrder);
    }

    /**
     * @throws Exception
     */
    private static function minSize(): float
    {
        return round(self::$champion->grind / self::currentPrice(), 2);
    }

    /**
     * @throws Exception
     */
    public static function currentPrice(): float
    {
        return round(self::binance()->getMarkPrice(), 2);
    }

    /**
     * @throws Exception
     */
    private static function binance(): ?Binance
    {
        if (!self::$binance) {
            self::$binance = new Binance(self::$champion->symbol);
        }

        return self::$binance;
    }

    private static function upsertOrder(array $data): void
    {
        $order = Order::query()->upsert([
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
                'champion_id' => self::$champion->id
            ]
        ],
            ['id'],
            ['status', 'avg_price', 'cum_qty', 'cum_quote', 'executed_qty', 'update_time', 'champion_id']
        );
    }

    public static function test(): void
    {
        dd(self::$champion);
    }

    /**
     * @throws Exception
     */
    private static function collectTrades(string $time): void
    {
        $binanceTrades = self::binance()->collectTrades(
            $time
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
            info('long profit');
            self::takeLongProfit();
        }
    }

    /**
     * @throws Exception
     */
    private static function maybeTakeShortProfit(): void
    {
        if (self::binance()->hasShortProfit()) {
            info('short profit');
            self::takeShortProfit();
        }
    }

    /**
     * @throws Exception
     */
    private static function takeLongProfit(): void
    {
        $order = self::getClosableLongOrder();

        if ($order) {
            $binanceOrder = self::binance()->closeLong(
                $order->orig_qty,
                self::currentPrice() + self::priceGap(),
            );

            self::upsertOrder($binanceOrder);
        }
    }

    /**
     * @throws Exception
     */
    private static function takeShortProfit(): void
    {

        $order = self::getClosableShortOrder();

        if ($order) {
            $binanceOrder = self::binance()->closeShort(
                $order->orig_qty,
                self::currentPrice() - self::priceGap(),
            );

            self::upsertOrder($binanceOrder);
        }
    }

    /**
     * @throws Exception
     */
    private static function getClosableLongOrder(): ?object
    {
        return Order::query()
            ->where('status', '=', Order::STATUS_FILLED)
            ->where('position_side', '=', Order::POSITION_SIDE_LONG)
            ->where('side', '=', Order::SIDE_BUY)
            ->where('avg_price', '<=', self::currentPrice())
            ->where('champion_id', '=', self::$champion->id)
            ->orderByDesc('update_time')
            ->first();
    }

    /**
     * @throws Exception
     */
    private static function getClosableShortOrder(): ?object
    {
        return Order::query()
            ->where('status', '=', Order::STATUS_FILLED)
            ->where('position_side', '=', Order::POSITION_SIDE_SHORT)
            ->where('side', '=', Order::SIDE_SELL)
            ->where('avg_price', '>=', self::currentPrice())
            ->where('champion_id', '=', self::$champion->id)
            ->orderByDesc('update_time')
            ->first();
    }

    /** @noinspection DuplicatedCode */
    /**
     * @throws Exception
     */
    private static function shouldOpenShort(): bool
    {
        if (self::binance()->hasShortProfit() || self::$champion->can_trade === false) {
            return false;
        }

        // Retrieve latest short order in last 2 hours
        return null === Order::query()
                ->where('status', '=', Order::STATUS_FILLED)
                ->where('position_side', '=', Order::POSITION_SIDE_SHORT)
                ->where('side', '=', Order::SIDE_SELL)
                ->where('symbol', '=', self::$champion->symbol)
                ->where('champion_id', '=', self::$champion->id)
                ->where('update_time', '>=', self::last2Hours())
                ->orderByDesc('update_time')
                ->first();
    }

    /** @noinspection DuplicatedCode */
    /**
     * @throws Exception
     */
    public static function shouldOpenLong(): bool
    {
        if (self::binance()->hasLongProfit() || self::$champion->can_trade === false) {
            return false;
        }

        // Retrieve latest long order in last 2 hours
        return null === Order::query()
                ->where('status', '=', Order::STATUS_FILLED)
                ->where('position_side', '=', Order::POSITION_SIDE_LONG)
                ->where('side', '=', Order::SIDE_BUY)
                ->where('symbol', '=', self::$champion->symbol)
                ->where('champion_id', '=', self::$champion->id)
                ->where('update_time', '>=', self::last2Hours())
                ->orderByDesc('update_time')
                ->first();
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
        self::importRecentTrades();

        $trades = Trade::query()
            ->where('realized_pnl', '>', 0)
            ->where('is_profit_collected', '=', false)
            ->get();

        if ($trades->count() > 0) {
            $trades->each(function ($trade) {
                ProfitManager::makeFromTrade($trade);
                $trade->update(['is_profit_collected' => true]);
            });
        }
    }

    /**
     * @throws Exception
     */
    public static function collectIncomes(): void
    {
        $incomes = self::binance()->income();

        foreach ($incomes as $income) {
            $income = [
                'tran_id' => $income['tranId'],
                'symbol' => $income['symbol'],
                'income_type' => $income['incomeType'],
                'income' => $income['income'],
                'asset' => $income['asset'],
                'time' => $income['time'],
                'trade_id' => $income['tradeId'] ?? null,
                'info' => $income['info'] ?? null,
                'created_at' => Carbon::createFromTimestampMs($income['time'])->toDateTimeString(),
            ];

            self::upsertIncome($income);
        }
    }

    /**
     * @throws Exception
     * @noinspection DuplicatedCode
     */
    public static function collectRecentIncomes(): void
    {
        tap(Income::query()
            ->orderByDesc('time')
            ->first(), function ($latestIncome) {
            $incomes = self::binance()->income($latestIncome->time);

            foreach ($incomes as $income) {
                $income = [
                    'tran_id' => $income['tranId'],
                    'symbol' => $income['symbol'],
                    'income_type' => $income['incomeType'],
                    'income' => $income['income'],
                    'asset' => $income['asset'],
                    'time' => $income['time'],
                    'trade_id' => $income['tradeId'] ?? null,
                    'info' => $income['info'] ?? null,
                ];

                self::upsertIncome($income);
            }
        });

    }

    private static function upsertIncome(array $income): void
    {
        Income::query()->upsert(
            $income,
            ['tran_id'],
            ['symbol', 'income_type', 'income', 'asset', 'time', 'trade_id', 'info', 'created_at']
        );
    }

    /**
     * @throws Exception
     */
    public static function useChampion(Champion $champion): void
    {
        self::$champion = $champion;

        self::updateBinanceInstance();
    }

    /**
     * @throws Exception
     */
    private static function updateBinanceInstance(): void
    {
        self::$binance = new Binance(self::$champion->symbol);
    }

    /**
     * @throws Exception
     */
    private static function priceGap(): float
    {
        return round(0.0004 * self::currentPrice(), 2);
    }
}
