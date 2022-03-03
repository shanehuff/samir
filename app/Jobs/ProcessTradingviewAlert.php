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
        $isSellSignal = '4h' === $this->alert->timeframe && 'sell' === $this->alert->side;

        if ($isSellSignal) {
            $this->commander->selling();
        }
    }
}
