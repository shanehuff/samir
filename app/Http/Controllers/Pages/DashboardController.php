<?php

namespace App\Http\Controllers\Pages;

use App\Services\Keisha;
use App\Trading\Income;
use App\Trading\Order;
use App\Trading\Profit;
use App\Trading\Trade;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Inertia\Response;
use Laravel\Jetstream\Jetstream;

class DashboardController
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

        $buys = Trade::query()
            ->where('side', Order::SIDE_BUY)
            ->get();

        $sells = Trade::query()
            ->where('side', Order::SIDE_SELL)
            ->get();

        $incomes = (float)$this->toVND(Income::all()->sum('income'));
        $netProfit = (float)$this->toVND($profits->sum('net_profit'));
        $fee = (float)$this->toVND($profits->sum('fee'));
        $apy = (($profits->sum('net_profit') + Income::all()->sum('income')) / $buys->sum('quote_qty') * 100)  / $this->getDays($profits->min('created_at')) * 365;

        return Jetstream::inertia()->render($request, 'Dashboard/Show', [
            'netProfit' => number_format($netProfit),
            'fee' => number_format($fee),
            'dealsCount' => $profits->count(),
            'upTime' => $this->getUpTime($profits->min('created_at')),
            'incomes' => number_format($incomes),
            'apy' => number_format($apy, 2) . '%',
            'revenue' => number_format($netProfit + $fee + $incomes),
            'avgRoe' => number_format($profits->avg('roe'), 2) . '%',
            'totalBuy' => number_format($this->toVND($buys->sum('quote_qty'))),
            'totalSell' => number_format($this->toVND($sells->sum('quote_qty'))),
        ]);
    }

    private function toVND($amount): string
    {
        return $amount * $this->vnd;
    }

    private function tryToLoadVND(): void
    {
        try {
            $this->vnd = (int)(new Keisha())->getPricing()->get('BUSD');
        } catch (GuzzleException $exception) {
            report($exception);
        }
    }

    private function getUpTime($date): string
    {
        return $date->diffForHumans();
    }

    private function uptimeInHours(mixed $min)
    {
        return $min->diffInHours(now());
    }

    private function getDays(mixed $min): float
    {
        return ceil($min->diffInDays(now()));
    }
}
