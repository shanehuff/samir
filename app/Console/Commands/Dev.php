<?php

namespace App\Console\Commands;

use App\Trading\Champion;
use App\Trading\ChampionManager;
use App\Trading\Income;
use App\Trading\TradingManager;
use Exception;
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
     * @param ChampionManager $championManager
     * @return void
     * @throws Exception
     */
    public function handle(ChampionManager $championManager): void
    {
        /** @var Champion $champion */
        $champion = Champion::query()->find(1);

        dd($champion->can_trade);

        $championManager->sync($champion);
    }
}