<?php

namespace App\Http\Controllers\Pages;

use App\Services\Keisha;
use App\Trading\Income;
use App\Trading\Profit;
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

        $incomes = (float)$this->toVND(Income::all()->sum('income'));
        $netProfit = (float)$this->toVND($profits->sum('net_profit'));
        $fee = (float)$this->toVND($profits->sum('fee'));

        return Jetstream::inertia()->render($request, 'Dashboard/Show', [
            'netProfit' => number_format($netProfit),
            'fee' => number_format($fee),
            'dealsCount' => $profits->count(),
            'upTime' => $this->getUpTime($profits->min('created_at')),
            'incomes' => number_format($incomes),
            'incomesPerHour' => number_format(($netProfit - $fee + $incomes) / $this->uptimeInHours($profits->min('created_at'))),
            'revenue' => number_format($netProfit + $fee + $incomes),
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
}
