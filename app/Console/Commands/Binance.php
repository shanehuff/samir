<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Binance\API;

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
     */
    public function handle()
    {
        $api = new API(
            config('services.binance.key'),
            config('services.binance.secret')
        );
        $balances = collect($api->balances());
        dd($balances->where('available', '>', 0)->toArray());
        return Command::SUCCESS;
    }
}
