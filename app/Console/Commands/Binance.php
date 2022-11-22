<?php

namespace App\Console\Commands;

use App\Dealers\Dealer;
use Exception;
use Illuminate\Console\Command;

class Binance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'binance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interacts With Binance API';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws Exception
     */
    public function handle(): int
    {
        dd((new Dealer)->shortPlan());

        return Command::SUCCESS;
    }
}
