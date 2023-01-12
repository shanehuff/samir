<?php

namespace App\Http\Controllers\Pages;

use App\Dealers\Dealer;
use App\Services\Keisha;
use Carbon\CarbonInterval;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Response;
use Laravel\Jetstream\Jetstream;

class DailyRoiController
{
    private int $vnd = 0;

    public function __construct()
    {
        $this->tryToLoadVND();
    }

    public function __invoke(Request $request): Response
    {
        $deals = Dealer::query()
            ->with('profit')
            ->whereHas('profit', function ($query) {
                $query->where('net_profit', '>', 0);
                $query->where('id', '>=', 101);
            })
            ->orderByDesc('updated_at')
            ->get();

        $groupedDeals = $deals->groupBy(function ($deal) {
            return $deal->updated_at->format('Y-m-d');
        });

        $dailyProfits = collect();
        $groupedDeals->each(function ($deal) use (&$dailyProfits) {
            if ($deal->count() > 0) {
                $dailyProfits->push((object)[
                    'net_profit' => $this->toVND($deal->sum('profit.net_profit')),
                    'day' => $deal->first()->updated_at->format('d/m'),
                    'roi' => number_format($deal->sum('profit.net_profit') / 20 * 100, 2)
                ]);
            }
        });

        return Jetstream::inertia()->render($request, 'DailyRoi/Show', [
            'deals' => $dailyProfits
        ]);
    }

    private function toVND($amount): string
    {
        return number_format($amount * $this->vnd, 0);
    }

    private function tryToLoadVND(): void
    {
        try {
            $this->vnd = (int)(new Keisha())->getPricing()->get('BUSD');
        } catch (GuzzleException $exception) {
            report($exception);
        }
    }
}
