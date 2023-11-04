<?php

namespace App\Http\Controllers\Pages;

use App\Services\Keisha;
use App\Trading\Order;
use App\Trading\Profit;
use GuzzleHttp\Exception\GuzzleException;
use Inertia\Inertia;
use Inertia\Response;

class ShowProfitController
{
    private int $vnd = 0;

    public function __construct()
    {
        $this->tryToLoadVND();
    }
    public function __invoke($profitId): Response
    {
        $profit = Profit::query()
            ->with('trade')
            ->where('id', $profitId)
            ->firstOrFail();

        $buy = $profit->trade->side === Order::SIDE_BUY ? $profit->trade : $profit->trade->counterTrade();
        $sell = $profit->trade->side === Order::SIDE_SELL ? $profit->trade : $profit->trade->counterTrade();

        // convert microsecond to duration in hours
        $duration = number_format(abs($buy->time - $sell->time) / 1000 / 60 / 60, 2);

        return Inertia::render('Profit/Show', [
            'profit' => $profit,
            'buy' => $buy,
            'sell' => $sell,
            'vnd' => $this->vnd,
            'duration' => $duration,
        ]);
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