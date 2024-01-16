<?php

namespace App\Console\Commands;

use App\Trading\ChampionManager;
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
     * @param ChampionManager $championManager
     * @return void
     * @throws Exception
     */
    public function handle(ChampionManager $championManager): void
    {

        $champions = $championManager->getActiveFarmers();

        $champions->each(function ($champion) {
            TradingManager::useChampion($champion);
            TradingManager::importRecentOrders();
        });

    }
}