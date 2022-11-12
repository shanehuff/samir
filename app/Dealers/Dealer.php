<?php

namespace App\Dealers;

use App\Binance\FuturesClient;
use Exception;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dealer extends Model
{
    use HasFactory;

    public const STATUS_NEW = 0;
    public const STATUS_ACTIVE = 1;
    public const STATUS_CLOSED = 2;

    protected $table = 'dealers';

    protected $guarded = [];

    protected FuturesClient $client;

    protected Collection $positions;

    protected array $long;

    protected array $short;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();

        $this->client = new FuturesClient(
            config('services.binance.key'),
            config('services.binance.secret')
        );

        $this->positions = $this->client->positions();
        $this->long = $this->positions->get(0);
        $this->short = $this->positions->get(1);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(DealerOrder::class);
    }

    public function trades(): HasMany
    {
        return $this->hasMany(DealerTrade::class);
    }

    public static function isInactive(): bool
    {
        return false === self::isActive();
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public static function current(): ?Dealer
    {
        return self::query()
            ->whereIn('status', [self::STATUS_NEW, self::STATUS_ACTIVE])
            ->orderByDesc('id')
            ->first();
    }

    public static function isActive(): bool
    {
        $dealer = self::current();

        return $dealer->id ?? false;
    }

    /**
     * @throws Exception
     */
    public function ordersOnBinance(): array
    {
        return $this->client->orders();
    }

    public function longPlan(): array
    {
        $plans = [];
        $steps = 3;
        $startSize = 0.02;
        $entry = $this->long['markPrice'] - 0.1;

        $plans[] = [
            'size' => $startSize,
            'entry' => (float)number_format($entry, 2),
            'total' => (float)number_format($startSize * $entry, 2)
        ];

        $planner = function ($_plans) {
            $limitMove = -0.0253339459778792;
            $_size = 0;
            $_entry = 0;

            foreach ($_plans as $plan) {
                $_size += $plan['size'];
            }

            foreach ($_plans as $plan) {
                $_entry += $plan['size'] * $plan['entry'] / $_size;
            }

            $output = [
                'size' => $_size * 2,
                'entry' => (float)number_format($_entry * (1 + $limitMove) + 0.02, 2)
            ];

            return [
                'size' => $output['size'],
                'entry' => $output['entry'],
                'total' => (float)number_format($output['size'] * $output['entry'], 2)
            ];
        };

        for ($i = 1; $i < $steps; $i++) {
            $plans[] = $planner($plans);
        }

        return $plans;
    }

    public function side(): string
    {
        if ((float)$this->long['positionAmt'] && (float)$this->short['positionAmt']) {
            return 'BOTH';
        }

        if ((float)$this->short['positionAmt']) {
            return 'SHORT';
        }

        return 'LONG';
    }

    public function positions(): array
    {
        $positions = [];

        if ('LONG' === $this->side()) {
            $positions[] = [
                'entry' => $this->long['entryPrice'],
                'size' => $this->long['positionAmt'],
                'profit' => $this->long['unRealizedProfit'],
                'liquidation' => $this->long['liquidationPrice']
            ];
        }

        return $positions;
    }

    /**
     * @throws Exception
     */
    public function toArray(): array
    {
        return [
            'active' => $this->isActive(),
            'side' => $this->side(),
            'positions' => $this->positions(),
            'orders_binance' => $this->ordersOnBinance(),
            'orders' => $this->orders->toArray()
        ];
    }

    /**
     * @throws Exception
     */
    public function executeLongPlan(): array
    {
        $orders = [];

        foreach ($this->longPlan() as $order) {
            $orders[] = $this->client->openLong($order['size'], $order['entry']);
        }

        if (count($orders)) {
            $this->store($orders);
        }

        return $orders;
    }

    public function store(array $orders)
    {
        $this->code = $orders[0]['orderId'];
        $this->status = self::STATUS_NEW;
        $this->side = $this->side();
        $this->save();
        $this->refresh();

        $time = [];

        foreach ($orders as $order) {
            $time[] = $order['updateTime'];
            $this->orders()->create([
                'binance_order_id' => $order['orderId'],
                'binance_client_id' => $order['clientOrderId'],
                'status' => DealerOrder::STATUS_NEW,
                'price' => $order['price'],
                'size' => $order['origQty']
            ]);
        }

        $this->binance_timestamp = min($time);
        $this->save();
    }

    public function hasNoPositionOnBinance(): bool
    {
        return 0.0 === (float)$this->positions()[0]['entry'];
    }

    /**
     * @throws Exception
     */
    public function takeProfitOrCancel()
    {
        // Cancel all orders if no position created
        if ($this->isActive() && $this->hasNoPositionOnBinance()) {
            try {
                $this->client->cancelAllOrders();
            } catch (Exception $exception) {
                info($exception->getMessage());
            }

            $this->status = self::STATUS_CLOSED;
            $this->save();

            $this->orders()->update([
                'status' => DealerOrder::STATUS_CLOSED
            ]);
        }
    }

    /**
     * @throws Exception
     */
    public static function openLongOrUpdate()
    {
        // Open long if dealer is inactive
        if (self::isInactive()) {
            /** @var Dealer $dealer */
            $dealer = self::query()
                ->create([
                    'code' => rand(),
                    'status' => self::STATUS_NEW,
                    'side' => 'LONG'
                ]);

            $dealer->executeLongPlan();
        } else {
            // Sync orders data between Samir and Binance
            /** @var Dealer $dealer */
            $dealer = self::current();

            $dealer->collectTrades();
            $dealer->updateOrderStatuses();

            // Position closed manually or liquidated. Get orders data and update Samir database.
            if ($dealer->hasNoPositionOnBinance()) {
                $dealer->close();
            }
        }
    }

    public function updateOrderStatuses()
    {
        $this->orders->each(/**
         * @throws Exception
         */ function ($order) {
            $binanceOrder = $this->client->getOrder($order->binance_order_id);

            // Update status
            $order->status = DealerOrder::STATUS[$binanceOrder['status']];
            $order->save();
        });
    }

    public function close()
    {
        $this->status = self::STATUS_CLOSED;
        $this->save();
    }

    public static function client(): FuturesClient
    {
        return (new self)->client;
    }

    /**
     * @throws Exception
     */
    public function collectTrades()
    {
        $trades = collect($this->client->userTrades($this->binance_timestamp));

        if ($trades->count()) {
            $trades->each(function ($trade) {
                $this->trades()
                    ->upsert([[
                        'binance_id' => $trade['id'],
                        'binance_order_id' => $trade['orderId'],
                        'dealer_id' => $this->id,
                        'symbol' => $trade['symbol'],
                        'side' => $trade['side'],
                        'price' => $trade['price'],
                        'size' => $trade['qty'],
                        'realized_pnl' => $trade['realizedPnl'],
                        'pnl_asset' => $trade['marginAsset'],
                        'total' => $trade['quoteQty'],
                        'fee' => $trade['commission'],
                        'fee_asset' => $trade['commissionAsset'],
                        'binance_timestamp' => $trade['time'],
                        'position_side' => $trade['positionSide'],
                        'buyer' => $trade['buyer'],
                        'maker' => $trade['maker'],
                    ]], ['binance_id']);
            });
        }
    }

    public function profit(): float|int
    {
        $profit = 0;
        $this->trades->each(function ($trade) use (&$profit) {
            $fee = 'BNB' === $trade->fee_asset ? $trade->fee * $trade->price : $trade->fee;

            $profit += -1 * $fee + $trade->realized_pnl;
        });

        return $profit;
    }
}