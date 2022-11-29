<?php

namespace App\Http\Controllers\Pages;

use App\Dealers\DealerProfit;
use App\Services\Keisha;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Inertia\Response;
use Laravel\Jetstream\Jetstream;

class StatsController
{
    private int $vnd = 0;

    public function __construct()
    {
        $this->tryToLoadVND();
    }

    public function __invoke(Request $request): Response
    {
        $profits = DealerProfit::all();

        return Jetstream::inertia()->render($request, 'Stats/Show', [
            'netProfit' => $this->toVND($profits->sum('net_profit')),
            'fee' => $this->toVND($profits->sum('fee')),
            'avgRoe' => number_format($profits->avg('roe') * 100, 2),
            'avgDuration' => number_format($profits->avg('duration') / 60, 2),
            'dealsCount' => $profits->count()
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