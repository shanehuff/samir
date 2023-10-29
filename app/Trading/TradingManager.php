<?php

namespace App\Trading;

use App\Binance\Binance;
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
        $activeBuyingOrders = self::buyingOrders();

        if (0 === $activeBuyingOrders->count()) {
            self::openLong();
        }
    }

    /**
     * @throws Exception
     */
    public static function handleUp(): void
    {
        $activeSellingOrders = self::sellingOrders();

        if (0 === $activeSellingOrders->count()) {
            self::openShort();
        }

        foreach($activeSellingOrders as $activeSellingOrder) {
            self::collectTrades($activeSellingOrder);
        }
    }

    /**
     * @throws Exception
     */
    private static function openLong(): void
    {
        $binanceOrder = self::binance()->openLong(
            self::minSize(),
            self::entryPrice() - 0.1,
        );

        self::createOrder($binanceOrder);
    }

    /**
     * @throws Exception
     */
    private static function openShort(): void
    {
        $binanceOrder = self::binance()->openShort(
            self::minSize(),
            self::entryPrice() + 0.1,
        );

        self::createOrder($binanceOrder);
    }

    private static function minSize(): float
    {
        return round(6 / self::entryPrice(), 2);
    }

    private static function entryPrice(): float
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

    private static function createOrder(array $data): void
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
            ['status']
        );
    }

    private static function sellingOrders(): Collection|array
    {
        return Order::query()
            ->where('status', 'IN', [Order::STATUS_NEW, Order::STATUS_FILLED])
            ->where('side', Order::SIDE_SELL)
            ->get();
    }

    private static function buyingOrders(): Collection|array
    {
        return Order::query()
            ->where('status', 'IN', [Order::STATUS_NEW, Order::STATUS_FILLED])
            ->where('side', Order::SIDE_BUY)
            ->get();
    }

    /**
     * @throws Exception
     */
    private static function collectTrades(mixed $order): void
    {
        $binanceTrades = self::binance()->collectTrades(
            $order->order_id
        );

        foreach($binanceTrades as $binanceTrade) {
            self::createTrade($binanceTrade);
        }
    }

    private static function createTrade(array $data): void
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
            ['status']
        );
    }

}