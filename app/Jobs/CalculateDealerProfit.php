<?php

namespace App\Jobs;

use App\Dealers\Dealer;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateDealerProfit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ?Dealer $dealer;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Dealer $dealer)
    {
        $this->dealer = $dealer;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        $this->dealer
            ->withBinanceApi()
            ->calculateProfit();
    }
}
