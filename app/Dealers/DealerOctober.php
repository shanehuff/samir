<?php

namespace App\Dealers;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DealerOctober extends Dealer
{

    /**
     * @throws Exception
     */
    public static function handleDown(): void
    {
        $dealer = self::instance();

        $dealer->openLong();
    }

    public static function instance(): Model|Builder|self
    {
        /** @var Dealer $dealer */
        $dealer = self::query()
            ->create([
                'code' => rand(),
                'status' => self::STATUS_NEW,
                'side' => 'LONG'
            ]);

        try {
            $dealer = $dealer->withBinanceApi();
        } catch (Exception $exception) {
            info('DealerOctober::handleDown: ' . $exception->getMessage());
        }

        return $dealer;
    }

    /**
     * @throws Exception
     */
    private function openLong(): void
    {
        $binanceOrder = $this->client->openLong(
            $this->getMinSize(),
            $this->getEntryPrice(),
        );

        $this->updateBinanceTimestamp($binanceOrder['updateTime']);

        $this->createOrder($binanceOrder);
    }

    private function getMinSize(): float|int
    {
        // convert number like 0.026616981634283 to 0.03
        return round(6 / $this->long['markPrice'], 2);
    }

    private function getEntryPrice(): float
    {
        return $this->long['markPrice'] - 0.1;
    }

    private function createOrder(array $binanceOrder): void
    {
        $this->orders()->upsert([
            [
                'dealer_id' => $this->id,
                'binance_order_id' => $binanceOrder['orderId'],
                'binance_client_id' => $binanceOrder['clientOrderId'],
                'status' => DealerOrder::STATUS_NEW,
                'price' => $binanceOrder['price'],
                'size' => $binanceOrder['origQty'],
            ]
        ],
            ['binance_order_id'],
            ['status']
        );
    }

    private function updateBinanceTimestamp(mixed $updateTime): void
    {
        $this->update([
            'binance_timestamp' => $updateTime
        ]);
    }
}