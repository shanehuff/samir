<?php

namespace App\Http\Controllers\Pages;

use App\Services\Keisha;
use App\Trading\Profit;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
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
        $profits = Profit::query()
            ->orderByDesc('id')
            ->get();

        $groupedProfits = $profits->groupBy(function ($profit) {
            return $profit->created_at->format('Y-m-d');
        });

        $dailyProfits = collect();
        $groupedProfits->each(function ($profit) use (&$dailyProfits) {
            if ($profit->count() > 0) {
                $dailyProfits->push((object)[
                    'net_profit' => $this->toVND($profit->sum('net_profit')),
                    'day' => $profit->first()->created_at->format('d/m'),
                    'roi' => number_format($profit->sum('net_profit') / 489.05 * 100, 2)
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
