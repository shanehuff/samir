<?php

namespace App\Http\Controllers\Pages;

use App\Services\Keisha;
use App\Trading\Income;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Inertia\Response;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Number;

class IncomesController
{
    private int $vnd = 0;

    public function __construct()
    {
        $this->tryToLoadVND();
    }

    public function __invoke(Request $request): Response
    {
        $incomes = Income::query()
            ->where('symbol', 'ETHUSDT')
            ->orderByDesc('id')
            ->limit(40)
            ->get();

        $incomes->map(function ($income) {
            $income->income = $this->toVND($income->income);
            $income->readable_time = $income->created_at->diffForHumans();
        });

        return Jetstream::inertia()->render($request, 'Incomes/Show', [
            'incomes' => $incomes
        ]);
    }

    private function toVND($amount): string
    {
        return Number::abbreviate($amount * $this->vnd);
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
