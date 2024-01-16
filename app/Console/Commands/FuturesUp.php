<?php

namespace App\Console\Commands;

use App\Trading\Champion;
use App\Trading\TradingManager;
use Exception;
use Illuminate\Console\Command;

class FuturesUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'futures:up';

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
        /** @var Champion $champion */
        $champion = Champion::query()->find(1);

        TradingManager::useChampion($champion);
        TradingManager::handleUp();
    }
}
