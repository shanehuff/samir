<?php

namespace App\Http\Controllers\Pages;

use App\Services\Keisha;
use App\Trading\Profit;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
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
            ->orderByDesc('id')
            ->get();

        $groupedProfits = $profits->groupBy(function ($profit) {
            return $profit->created_at->format('Y-m');
        });

        $monthlyProfits = collect();
        $groupedProfits->each(function ($profit) use (&$monthlyProfits) {
            if ($profit->count() > 0) {
                $monthlyProfits->push((object)[
                    'net_profit' => $this->toVND($profit->sum('net_profit')),
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
