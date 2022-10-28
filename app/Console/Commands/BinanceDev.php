<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use App\Binance\ApiClient;

class BinanceDev extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'binance:dev';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interacts With Binance API (Development)';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws Exception
     */
    public function handle()
    {
        $api = new ApiClient(
            config('services.binance.key'),
            config('services.binance.secret')
        );

        dd(collect($api->futures())->get('totalMarginBalance'));
    }
}
