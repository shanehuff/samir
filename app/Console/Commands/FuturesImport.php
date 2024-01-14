<?php

namespace App\Console\Commands;

use App\Trading\TradingManager;
use Exception;
use Illuminate\Console\Command;

class FuturesImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'futures:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interacts With Binance FuturesStatus API (Development)';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        TradingManager::importRecentOrders();
    }
}