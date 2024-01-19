<?php

namespace App\Console\Commands;

use App\Trading\Champion;
use App\Trading\SpotTradingManager;
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
    public function handle(SpotTradingManager $spotTradingManager): void
    {
        /** @var Champion $champion */
        $champion = Champion::query()->find(4);
        $spotTradingManager
            ->useChampion($champion)
            ->syncOrdersFromExchange();

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