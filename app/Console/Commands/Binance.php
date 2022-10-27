<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Binance\ApiClient;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
        $api = new ApiClient(
            config('services.binance.key'),
            config('services.binance.secret')
        );

        dd(collect($api->saving()));

        $balances = collect($api->balances())
            ->where('available', '>', 0);

        if ($balances->count() > 0) {
            $data = [];
            $balances->each(function ($balance, $symbol) use (&$data) {
                $data[] = [
                    'symbol' => $symbol,
                    'available' => $balance['available'],
                    'on_order' => $balance['onOrder'],
                    'updated_at' => Carbon::now()
                ];
            });

            DB::table('balances')
                ->upsert(
                    $data,
                    ['symbol'],
                    ['available', 'on_order', 'updated_at']
                );
        }

        return Command::SUCCESS;
    }
}
