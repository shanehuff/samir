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

    public function placeBuyOrder(float $price): void
    {
        $binanceOrder = $this->client()->buy(
            $this->champion->symbol,
            round($this->champion->grind / $price, 2),
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
    public function syncOrdersFromExchange(): void
    {
        $order = SpotOrder::query()
            ->where('champion_id', '=', $this->champion->id)
            ->where('status', '=', SpotOrder::STATUS_NEW)
            ->first();

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
    }
}
