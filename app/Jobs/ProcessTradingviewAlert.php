<?php

namespace App\Jobs;

use App\Models\Commander;
use App\Models\TradingviewAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTradingviewAlert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public TradingviewAlert $alert;

    public Commander $commander;

    public const ACTION_SELL_MODE = 0;
    public const ACTION_BUY_MODE = 2;
    public const ACTION_CHILL_MODE = 3;
    public const ACTION_UNKNOWN = 4;
    public const ACTION_BUY = 5;
    public const ACTION_SELL = 6;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(TradingviewAlert $alert, Commander $commander)
    {
        $this->alert = $alert;
        $this->commander = $commander;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        match ($this->resolveActionFromAlert($this->alert)) {

            self::ACTION_SELL_MODE => $this->commander->selling(),

            self::ACTION_BUY_MODE => $this->commander->buying(),

            self::ACTION_CHILL_MODE => $this->commander->chilling(),

            self::ACTION_BUY => $this->commander->buy($this->alert->price),

            self::ACTION_SELL => $this->commander->sell($this->alert->price),

            default => info(sprintf(
                'Receive unknown action from alert: %s',
                $this->alert->toJson()
            ))
        };
    }

    public function resolveActionFromAlert(TradingviewAlert $alert): int
    {
        if ('4h' === $alert->timeframe && 'sell' === $alert->side) {
            return self::ACTION_SELL_MODE;
        }

        if ('4h' === $alert->timeframe && 'chill' === $alert->side) {
            return self::ACTION_CHILL_MODE;
        }

        if ('4h' === $alert->timeframe && 'buy' === $alert->side) {
            return self::ACTION_BUY_MODE;
        }

        if ('5m' === $alert->timeframe && 'buy' === $alert->side) {
            return self::ACTION_BUY;
        }

        if ('5m' === $alert->timeframe && 'sell' === $alert->side) {
            return self::ACTION_SELL;
        }

        return self::ACTION_UNKNOWN;
    }
}
