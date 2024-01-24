<?php

namespace App\Console\Commands;

use App\Trading\Champion;
use App\Trading\SpotTradingManager;
use Exception;
use Illuminate\Console\Command;

use App\Trading\ChampionManager;

class AccountCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account';

    protected ChampionManager $championManager;

    protected SpotTradingManager$spotTradingManager;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interacts With Accounts';

    public function __construct(ChampionManager $championManager, SpotTradingManager $spotTradingManager)
    {
        $this->championManager = $championManager;
        $this->spotTradingManager = $spotTradingManager;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        $this->syncActiveFarmers();
        $this->syncActiveLootcycles();
    }

    public function syncActiveLootcycles()
    {
        $champions = $this->championManager->getActiveLootcycles();

        $champions->each(function($champion) {
            $this->spotTradingManager
                ->useChampion($champion)
                ->syncOrdersFromExchange()
                ->collectTrades();

            $this->championManager->syncLootcycle($champion);
        });
    }

    public function syncActiveFarmers()
    {
        $champions = $this->championManager->getActiveFarmers();

        $champions->each(function($champion) {
            $this->championManager->sync($champion);
        });
    }
}
