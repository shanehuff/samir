<?php

namespace App\Console\Commands;

use App\Trading\Champion;
use App\Trading\TradingManager;
use Exception;
use Illuminate\Console\Command;

class FuturesProfits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'futures:profits';

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
        info('Collect profits & incomes');
        /** @var Champion $champion */

        $champion = Champion::query()->find(1);

        TradingManager::useChampion($champion);
        TradingManager::collectProfits();
        TradingManager::collectRecentIncomes();
    }
}
