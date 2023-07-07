<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use App\Binance\ApiClient;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BinanceCollectBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'binance:balances';

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
        $data = [];
        $api = new ApiClient(
            config('services.binance.key'),
            config('services.binance.secret')
        );

        $futuresAssets = collect($api->futures()['assets']);

        $riskyMargin = $futuresAssets
            ->where('asset', '=', 'USDT')
            ->sum('initialMargin');

        $data['FU'] = [
            'symbol' => 'FU',
            'available' => $riskyMargin,
            'on_order' => 0,
            'updated_at' => Carbon::now()
        ];

        $availableUSDT = $futuresAssets
            ->where('asset', '=', 'USDT')
            ->sum('availableBalance');

        $data['FUSDT'] = [
            'symbol' => 'FUSDT',
            'available' => $availableUSDT,
            'on_order' => 0,
            'updated_at' => Carbon::now()
        ];

        // Collect loans data
        $loans = collect($api->loans())->get('rows');

        foreach($loans as $loan) {
            $loanSymbol = $loan['loanCoin'];

            if (isset($data[$loanSymbol])) {
                $data[$loanSymbol] = [
                    'symbol' => $loanSymbol,
                    'available' => $data[$loanSymbol]['available'] + 0,
                    'on_order' => $data[$loanSymbol]['on_order'] - $loan['totalDebt'],
                    'updated_at' => Carbon::now()
                ];
            } else {
                $data[$loanSymbol] = [
                    'symbol' => $loanSymbol,
                    'available' => 0,
                    'on_order' => 0 - $loan['totalDebt'],
                    'updated_at' => Carbon::now()
                ];
            }

            $collateralSymbol = $loan['collateralCoin'];

            if (isset($data[$collateralSymbol])) {
                $data[$collateralSymbol] = [
                    'symbol' => $collateralSymbol,
                    'available' => $data[$collateralSymbol]['available'] + 0,
                    'on_order' => $data[$collateralSymbol]['on_order'] + $loan['collateralAmount'],
                    'updated_at' => Carbon::now()
                ];
            } else {
                $data[$collateralSymbol] = [
                    'symbol' => $collateralSymbol,
                    'available' => 0,
                    'on_order' => 0 + $loan['collateralAmount'],
                    'updated_at' => Carbon::now()
                ];
            }
        }

        // Collect defi farming data
        $farmings = collect($api->farming())->where('share.shareAmount', '>', 0);

        $farmings->each(function ($farming) use (&$data) {
            foreach ($farming['share']['asset'] as $symbol => $amount) {
                if (isset($data[$symbol])) {
                    $data[$symbol] = [
                        'symbol' => $symbol,
                        'available' => $data[$symbol]['available'] + $amount,
                        'on_order' => $data[$symbol]['on_order'] + 0,
                        'updated_at' => Carbon::now()
                    ];
                } else {
                    $data[$symbol] = [
                        'symbol' => $symbol,
                        'available' => $amount,
                        'on_order' => 0,
                        'updated_at' => Carbon::now()
                    ];
                }
            }
        });

        // Collect locked savings data
        $savings = collect($api->saving())->groupBy('asset');

        $savings->each(function ($saving) use (&$data) {
            $symbol = $saving->first()['asset'];

            if (isset($data[$symbol])) {
                $data[$symbol] = [
                    'symbol' => $symbol,
                    'available' => $data[$symbol]['available'] + 0,
                    'on_order' => $data[$symbol]['on_order'] + $saving->sum('amount'),
                    'updated_at' => Carbon::now()
                ];
            } else {
                $data[$symbol] = [
                    'symbol' => $symbol,
                    'available' => 0,
                    'on_order' => $saving->sum('amount'),
                    'updated_at' => Carbon::now()
                ];
            }
        });

        // Then collect spot balances
        $balances = collect($api->balances())
            ->where('available', '>', 0);

        if ($balances->count() > 0) {
            $balances->each(function ($balance, $symbol) use (&$data) {
                $symbol = preg_replace('/^LD/', '', $symbol);
                if (isset($data[$symbol])) {
                    $data[$symbol] = [
                        'symbol' => $symbol,
                        'available' => $data[$symbol]['available'] + $balance['available'],
                        'on_order' => $data[$symbol]['on_order'] + $balance['onOrder'],
                        'updated_at' => Carbon::now()
                    ];
                } else {
                    $data[$symbol] = [
                        'symbol' => $symbol,
                        'available' => $balance['available'],
                        'on_order' => $balance['onOrder'],
                        'updated_at' => Carbon::now()
                    ];
                }
            });

            // Truncate table balances
            DB::table('balances')->truncate();

            // Insert balances
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
