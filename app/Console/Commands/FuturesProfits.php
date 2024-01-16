<?php

namespace App\Console\Commands;

use App\Trading\Champion;
use App\Trading\ChampionManager;
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
    public function handle(ChampionManager $championManager): void
    {
        info('Collect profits & incomes');

        $champions = $championManager->getActiveFarmers();

        $champions->each(function($champion) {
            TradingManager::useChampion($champion);
            TradingManager::collectProfits();
            TradingManager::collectRecentIncomes();
        });
    }
}
