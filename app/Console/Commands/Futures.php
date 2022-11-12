<?php

namespace App\Console\Commands;

use App\Dealers\Dealer;
use Exception;
use Illuminate\Console\Command;

class Futures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'futures';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interacts With Binance Futures API (Development)';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws Exception
     */
    public function handle()
    {
        tap(Dealer::current(), function($dealer) {
            dd($dealer->profit());
        });
    }
}
