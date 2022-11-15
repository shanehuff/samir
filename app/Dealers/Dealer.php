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
    public function executeLongPlan(): array
    {
        $orders = [];

        try {
            foreach ($this->longPlan() as $order) {
                $orders[] = $this->client->openLong($order['size'], $order['entry']);
            }
        } catch (Exception $exception) {
            info(sprintf('Failed creating Binance orders: %s', $exception->getMessage()));
        }

        if (count($orders)) {
            $this->tapDb();
            $this->createOrders($orders);
        }

        return $orders;
    }

    private function tapDb()
    {
        $this->code = 0;
        $this->status = self::STATUS_NEW;
        $this->side = $this->side();
        $this->save();
        $this->refresh();

        return $this;
    }

    public function createOrders(array $orders)
    {
        $this->code = $orders[0]['orderId'];

        $time = [];

        foreach ($orders as $order) {
            $time[] = $order['updateTime'];
            $this->orders()->upsert([
                [
                    'dealer_id' => $this->id,
                    'binance_order_id' => $order['orderId'],
                    'binance_client_id' => $order['clientOrderId'],
                    'status' => DealerOrder::STATUS_NEW,
                    'price' => $order['price'],
                    'size' => $order['origQty']
                ]
            ],
                ['binance_order_id'],
                ['status']
            );
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
    public static function takeProfitOrCancel()
    {
        if (self::isActive()) {
            $dealer = self::current();

            $dealer->updateOrderStatuses();
            $dealer->collectTrades();

            if ($dealer->hasNoPositionOnBinance()) {
                // Cancel all orders if no position created
                try {
                    $dealer->client->cancelAllOrders();
                } catch (Exception $exception) {
                    info($exception->getMessage());
                }

                $dealer->close();
            } else {
                $dealer->maybeTakeProfit();
            }
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

    private function updateOrderStatuses()
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

    private function close()
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
    private function collectTrades()
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

    public function profit(): array
    {
        $profit = [
            'realized_profit' => 0,
            'fee' => 0,
            'net_profit' => 0
        ];
        $total = 0;
        $this->trades->each(function ($trade) use (&$profit, &$total) {
            $fee = -1 * ('BNB' === $trade->fee_asset ? $trade->fee * $trade->price : $trade->fee);

            $profit['realized_profit'] += $trade->realized_pnl;
            $profit['fee'] += $fee;
            $profit['net_profit'] += $fee + $trade->realized_pnl;

            if ($trade->buyer) {
                $total += $trade->total;
            }
        });

        $profit['roe'] = number_format($profit['net_profit'] / $total * 100, 2) . '%';

        return $profit;
    }

    /**
     * @throws Exception
     */
    private function maybeTakeProfit()
    {
        if (isset($this->positions()[0]['profit']) && $this->positions()[0]['profit'] >= 0) {
            info('Executing short plan');
            $this->executeShortPlan(); // @TODO Should be changed to executeTakeProfitPlan later
        } else {
            info(sprintf('Profit Debug: %s', json_encode($this->positions())));
        }
    }

    public function shortPlan(): array
    {
        $plans = [];
        $size = $this->firstOrder()->size;
        $count = $this->countFilledOrders();
        $steps = $this->long['positionAmt'] / ($size ?? 0.02) / $count;
        $startSize = 0.02;
        $entry = $this->short['markPrice'] + 0.1;

        $plans[] = [
            'size' => $startSize,
            'entry' => (float)number_format($entry, 2),
            'total' => (float)number_format($startSize * $entry, 2)
        ];

        $planner = function ($_plans) {
            $limitMove = 0.0256000000000001;
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
                'entry' => (float)number_format($_entry * (1 + $limitMove) - 0.02, 2)
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

        for ($i = 0; $i < count($plans); $i++) {
            $sizes[] = $plans[$i]['size'];
        }

        rsort($sizes);

        for ($i = 0; $i < count($plans); $i++) {
            $plans[$i]['size'] = $sizes[$i];
        }

        return $plans;
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    private function firstOrder(): ?DealerOrder
    {
        return $this->orders()
            ->orderBy('id')
            ->first();
    }

    private function countFilledOrders(): int
    {
        return $this->orders()
            ->where('status', DealerOrder::STATUS_FILLED)
            ->count();
    }

    /**
     * @throws Exception
     */
    private function executeShortPlan(): array
    {
        $orders = [];

        foreach ($this->shortPlan() as $order) {
            $orders[] = $this->client->closeLong($order['size'], $order['entry']);
        }

        if (count($orders)) {
            $this->createOrders($orders);
        }

        return $orders;
    }
}