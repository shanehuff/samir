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
            ->get();

        $groupedIncomes = $incomes->groupBy(function ($income) {
            return $income->created_at->format('Y-m-d');
        });

        $outputIncomes = collect();

        $groupedIncomes->map(function ($income) use(&$outputIncomes) {
            $outputIncomes->push((object)[
                'income' => $this->toVND($income->sum('income')),
                'day' => $income->first()->created_at->format('d/m')
            ]);
        });

        return Jetstream::inertia()->render($request, 'Incomes/Show', [
            'incomes' => $outputIncomes
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
