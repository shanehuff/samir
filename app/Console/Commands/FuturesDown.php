<?php

namespace App\Console\Commands;

use App\Dealers\DealerOctober;
use Exception;
use Illuminate\Console\Command;

class FuturesDown extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'futures:down';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interacts With Binance FuturesDown API (Development)';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        DealerOctober::handleDown();
    }
}
