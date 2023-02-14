<?php

namespace App\Http\Controllers\Pages;

use App\Dealers\DealerProfit;
use App\Services\Keisha;
use Carbon\CarbonInterval;
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
        $profits = DealerProfit::where('id', '>=', 189)->get();

        return Jetstream::inertia()->render($request, 'Dashboard/Show', [
            'netProfit' => $this->toVND($profits->sum('net_profit')),
            'fee' => $this->toVND($profits->sum('fee')),
            'dealsCount' => $profits->count(),
            'initCapital' => $this->toVND(40),
            'upTime' => $this->getUpTime($profits->min('created_at')),
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

    private function getUpTime($date): string
    {
        return $date->diffForHumans();
    }
}
