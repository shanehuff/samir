<?php

namespace App\Trading;

use App\Binance\SpotClient;
use Exception;

class SpotTradingManager
{
    private ?Champion $champion = null;

    private ?SpotClient $client = null;

    public function useChampion(Champion $champion): static
    {
        $this->champion = $champion;

        return $this;
    }

    public function client(): ?SpotClient
    {
        if (is_null($this->client)) {
            $this->client = new SpotClient(
                config('services.binance.key'),
                config('services.binance.secret')
            );
        }

        return $this->client;
    }

    private function getMinSize(float $price)
    {
        $afterPoint = 'GMTUSDT' === $this->champion->symbol ? 1 : 2;
        $size = round($this->champion->grind / $price, $afterPoint);

        if ('BNBUSDT' === $this->champion->symbol) {
            return $size > 0.02 ? $size : 0.02;
        }

        return $size;
    }

    public function maybePlaceSellOrder(float $price): void
    {
        if ($price >= $this->champion->entry) {
            $binanceOrder = $this->client()->sell(
                $this->champion->symbol,
                $this->getMinSize($price),
                $price
            );

            if ($binanceOrder) {
                $this->upsertSpotOrder($binanceOrder);
            }
        }
    }

    public function placeBuyOrder(float $price): void
    {
        $binanceOrder = $this->client()->buy(
            $this->champion->symbol,
            $this->getMinSize($price),
            $price
        );

        if ($binanceOrder) {
            $this->upsertSpotOrder($binanceOrder);
        }
    }

    private function upsertSpotOrder(array $data): void
    {
        SpotOrder::query()->upsert([
            [
                'order_id' => $data['orderId'],
                'symbol' => $data['symbol'],
                'status' => SpotOrder::STATUS[$data['status']],
                'client_order_id' => $data['clientOrderId'],
                'price' => $data['price'],
                'orig_qty' => $data['origQty'],
                'executed_qty' => $data['executedQty'],
                'cummulative_quote_qty' => $data['cummulativeQuoteQty'],
                'time_in_force' => $data['timeInForce'],
                'type' => $data['type'],
                'side' => $data['side'],
                'self_trade_prevention_mode' => $data['selfTradePreventionMode'],
                'champion_id' => $this->champion->id,
                'working_time' => $data['workingTime'],
                'fills' => isset($data['fills']) ? json_encode($data['fills']) : null,
                'transact_time' => $data['transactTime'] ?? null,
                'update_time' => $data['updateTime'] ?? null,
                'order_list_id' => $data['orderListId'],
                'orig_quote_order_qty' => $data['origQuoteOrderQty'] ?? null,
                'is_working' => $data['isWorking'] ?? null,
                'iceberg_qty' => $data['icebergQty'] ?? null,
                'stop_price' => $data['stopPrice'] ?? null
            ]
        ],
            ['id'],
            ['status', 'cummulative_quote_qty', 'executed_qty', 'working_time', 'champion_id', 'stop_price', 'iceberg_qty', 'update_time', 'order_list_id', 'orig_quote_order_qty', 'is_working']
        );
    }

    /**
     * @throws Exception
     */
    public function syncOrdersFromExchange(): static
    {
        $order = SpotOrder::query()
            ->where('champion_id', '=', $this->champion->id)
            ->where('status', '=', SpotOrder::STATUS_NEW)
            ->first();

        if (is_null($order)) {
            $order = SpotOrder::query()
                ->where('symbol', $this->champion->symbol)
                ->orderByDesc('order_id')
                ->first();
        }

        if ($order) {
            $exchangeOrders = $this->client()->orders(
                $this->champion->symbol,
                500,
                $order->order_id
            );

            foreach ($exchangeOrders as $exchangeOrder) {
                $this->upsertSpotOrder($exchangeOrder);
            }
        }

        return $this;
    }

    public function collectTrades()
    {
        $orders = SpotOrder::query()
            ->where('status', '=', SpotOrder::STATUS_FILLED)
            ->where('is_trades_collected', '=', false)
            ->get();

        if ($orders->count() > 0) {
            $orders->each(function ($order) {
                $trades = $this->client()->getOrderTrades($order->symbol, $order->order_id);

                $this->upsertTrades($trades);

                $order->update([
                    'is_trades_collected' => true
                ]);
            });
        }
    }

    public function upsertTrades(array $trades)
    {
        if (count($trades) > 0) {
            foreach ($trades as $data) {
                SpotTrade::query()->upsert([
                    [
                        'binance_trade_id' => $data['id'],
                        'order_id' => $data['orderId'],
                        'symbol' => $data['symbol'],
                        'order_list_id' => $data['orderListId'],
                        'price' => $data['price'],
                        'qty' => $data['qty'],
                        'quote_qty' => $data['quoteQty'],
                        'commission' => $data['commission'],
                        'commission_asset' => $data['commissionAsset'],
                        'time' => $data['time'],
                        'is_buyer' => $data['isBuyer'],
                        'is_maker' => $data['isMaker'],
                        'is_best_match' => $data['isBestMatch'],
                    ]
                ],
                    ['binance_trade_id'],
                    ['price', 'qty', 'quote_qty', 'time', 'is_buyer', 'commission']
                );
            }
        }
    }

    public function noRecentBuySpotOrder(Champion $champion)
    {
        $order = SpotOrder::query()
            ->where('champion_id', '=', $champion->id)
            ->where('side', '=', SpotOrder::SIDE_BUY)
            ->orderByDesc('created_at')
            ->first();

        if ($order) {
            // Make sure no order placed last 2 hours
            return now()->diffInHours($order->created_at) >= 2;
        }

        return true;
    }

    public function noRecentSellSpotOrder(Champion $champion)
    {
        $order = SpotOrder::query()
            ->where('champion_id', '=', $champion->id)
            ->where('side', '=', SpotOrder::SIDE_SELL)
            ->orderByDesc('created_at')
            ->first();

        if ($order) {
            // Make sure no order placed last 2 hours
            return now()->diffInHours($order->created_at) > 2;
        }

        return true;
    }
}
