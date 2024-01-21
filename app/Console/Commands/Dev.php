<?php

namespace App\Console\Commands;

use App\Trading\Champion;
use App\Trading\SpotTradingManager;
use App\Trading\ChampionManager;
use Illuminate\Console\Command;

class Dev extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run code for development purposes';

    /**
     * Execute the console command.
     *
     * @param SpotTradingManager $spotTradingManager
     * @return void
     */
    public function handle(SpotTradingManager $spotTradingManager, ChampionManager $championManager): void
    {
        $champions = $championManager->getActiveLootcycles();

        $champions->each(function($champion) use ($championManager) {
            $championManager->syncLootcycle($champion);
        });
    }

    public function syncOrderAndTrades($spotTradingManager, $championManager)
    {
        /** @var Champion $champion */
        $champion = Champion::query()->find(4);
        $data = $spotTradingManager
            ->useChampion($champion)
            ->syncOrdersFromExchange()
            ->collectTrades();
    }

    /**
     * Execute the console command.
     *
     * @param SpotTradingManager $spotTradingManager
     * @return void
     */
    public function buy(SpotTradingManager $spotTradingManager): void
    {
        /** @var Champion $champion */
        $champion = Champion::query()->find(4);
        $price = 313.6;

        $spotTradingManager
            ->useChampion($champion)
            ->placeBuyOrder($price);

    }

}