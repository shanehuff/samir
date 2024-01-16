<?php

namespace App\Console\Commands;

use App\Trading\Champion;
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

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interacts With Accounts';

    public function __construct(ChampionManager $championManager)
    {
        $this->championManager = $championManager;

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
        $champions = $this->championManager->getActiveFarmers();

        $champions->each(function($champion) {
            $this->championManager->sync($champion);
        });

    }
}
