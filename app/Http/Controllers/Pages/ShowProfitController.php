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

        // get readable duration from buy to sell
        $duration = $this->getReadableDuration($buy->time, $sell->time);

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

    private function getReadableDuration(mixed $created_at, mixed $created_at1): string
    {
        $duration = number_format(abs($created_at - $created_at1) / 1000 / 60 / 60, 2);

        // convert $duration to readable format like 1h 30m
        return floor($duration) . 'h ' . floor(($duration - floor($duration)) * 60) . 'm';
    }
}