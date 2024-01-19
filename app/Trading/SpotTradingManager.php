<?php

namespace App\Trading;

use App\Binance\SpotClient;

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

    public function placeBuyOrder(float $price)
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
                'status' => Order::STATUS[$data['status']],
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
                'fills' => json_encode($data['fills']),
                'transact_time' => $data['transactTime']
            ]
        ],
            ['id'],
            ['status', 'cummulative_quote_qty', 'executed_qty', 'working_time', 'champion_id']
        );
    }
}
