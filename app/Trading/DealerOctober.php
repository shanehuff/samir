<?php

namespace App\Dealers;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DealerOctober extends Dealer
{
    public static function instance($side = 'LONG'): Model|Builder|self
    {
        $dealer = self::query()
            ->where('side', $side)
            ->where('status', self::STATUS_NEW)
            ->first();

        return $dealer ?? self::query()
            ->create([
                'code' => rand(),
                'status' => self::STATUS_NEW,
                'side' => $side,
            ]);
    }

    /**
     * @throws Exception
     */
    public function openShort(): void
    {
        $binanceOrder = $this->binance->openShort(
            $this->getMinSize(),
            $this->getEntryPrice() + 0.1,
        );

        $this->createOrder($binanceOrder);
    }

    /**
     * @throws Exception
     */
    public function openLong(): void
    {
        try {
            $binanceOrder = $this->binance->openLong(
                $this->getMinSize(),
                $this->getEntryPrice() - 0.1,
            );

            $this->updateBinanceTimestamp($binanceOrder['updateTime']);

            $this->createOrder($binanceOrder);
        } catch (Exception $e) {
            info($e->getMessage());
            info(json_encode(['size' => $this->getMinSize(), 'entry' => $this->getEntryPrice() - 0.1]));
        }

    }

    private function getMinSize(): float|int
    {
        // convert number like 0.026616981634283 to 0.03
        return round(6 / $this->binance->getMarkPrice(), 2);
    }

    private function getEntryPrice(): float
    {
        return $this->binance->getMarkPrice();
    }

    private function createOrder(array $data): void
    {
        $this->orders()->upsert([
            [
                'dealer_id' => $this->id,
                'binance_order_id' => $data['orderId'],
                'binance_client_id' => $data['clientOrderId'],
                'binance_timestamp' => $data['updateTime'],
                'status' => Order::STATUS_NEW,
                'price' => $data['price'],
                'size' => $data['origQty'],
            ]
        ],
            ['binance_order_id'],
            ['status']
        );
    }
}