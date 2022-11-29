<?php

namespace App\Http\Controllers\Pages;

use App\Dealers\Dealer;
use App\Services\Keisha;
use Carbon\CarbonInterval;
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
        $deals = Dealer::with('profit')
            ->whereHas('profit', function ($query) {
                $query->where('net_profit', '>', 0);
            })
            ->orderByDesc('created_at')
            ->get();

        $deals->map(function ($deal) {
            if ($deal->profit) {
                $deal->net_profit = $this->toVND($deal->profit->net_profit);
                $deal->duration = CarbonInterval::minutes($deal->profit->duration)->cascade()->forHumans();
                $deal->readable_time = $deal->created_at->diffForHumans();
            }
        });

        return Jetstream::inertia()->render($request, 'Deals/Show', [
            'deals' => $deals
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