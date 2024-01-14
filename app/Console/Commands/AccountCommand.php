<?php

namespace App\Console\Commands;

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

    protected $championManager;

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
        $this->championManager->sync(1);
    }
}
