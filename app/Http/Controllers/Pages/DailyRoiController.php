<?php

namespace App\Http\Controllers\Pages;

use App\Services\Keisha;
use App\Trading\Income;
use App\Trading\Profit;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Number;
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

        $groupedIncomes = Income::query()
            ->orderByDesc('id')
            ->get()
            ->groupBy(function ($income) {
                return $income->created_at->format('Y-m-d');
            });

        $dailyProfits = collect();
        $groupedProfits->each(function ($profit) use (&$dailyProfits, $groupedIncomes) {
            if ($profit->count() > 0) {
                $income = $groupedIncomes->get($profit->first()->created_at->format('Y-m-d'));
                $income = $income ? $income->sum('income') : 0;

                $dailyProfits->push((object)[
                    'net_profit' => $this->toVND($profit->sum('net_profit') + $income),
                    'day' => $profit->first()->created_at->format('d/m'),
                    'count' => $profit->count(),
                ]);
            }
        });

        return Jetstream::inertia()->render($request, 'DailyRoi/Show', [
            'deals' => $dailyProfits
        ]);
    }

    private function toVND($amount): string
    {
        return Number::abbreviate($amount * $this->vnd);
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
