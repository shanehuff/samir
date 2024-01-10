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

class MonthlyRoiController
{
    private int $vnd = 0;

    public function __construct()
    {
        $this->tryToLoadVND();
    }

    public function __invoke(Request $request): Response
    {
        $profits = Profit::query()
            ->where('symbol', 'ETHUSDT')
            ->orderByDesc('id')
            ->get();

        $groupedProfits = $profits->groupBy(function ($profit) {
            return $profit->created_at->format('Y-m');
        });

        $groupedIncomes = Income::query()
            ->where('symbol', 'ETHUSDT')
            ->orderByDesc('id')
            ->get()
            ->groupBy(function ($income) {
                return $income->created_at->format('Y-m');
            });

        $monthlyProfits = collect();
        $groupedProfits->each(function ($profit) use (&$monthlyProfits, $groupedIncomes) {
            if ($profit->count() > 0) {
                $income = $groupedIncomes->get($profit->first()->created_at->format('Y-m'));
                $income = $income ? $income->sum('income') : 0;
                
                $monthlyProfits->push((object)[
                    'net_profit' => $this->toVND($profit->sum('net_profit') + $income),
                    'month' => $profit->first()->created_at->format('m/Y')
                ]);
            }
        });

        return Jetstream::inertia()->render($request, 'MonthlyRoi/Show', [
            'deals' => $monthlyProfits
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
