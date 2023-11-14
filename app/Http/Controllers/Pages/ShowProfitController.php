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

        return Inertia::render('Profit/Show', [
            'profit' => $profit,
            'vnd' => $this->vnd,
            'roi' => number_format($profit->roe, 2) . '%',
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