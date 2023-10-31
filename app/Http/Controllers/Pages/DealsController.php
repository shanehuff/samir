<?php

namespace App\Http\Controllers\Pages;

use App\Services\Keisha;
use App\Trading\Profit;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Inertia\Response;
use Laravel\Jetstream\Jetstream;

class DealsController
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
            ->limit(40)
            ->get();

        $profits->map(function ($profit) {
            $profit->net_profit = $this->toVND($profit->net_profit);
            $profit->readable_time = $profit->created_at->diffForHumans();
        });

        return Jetstream::inertia()->render($request, 'Deals/Show', [
            'deals' => $profits
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
