<?php

namespace App\Commander;

use App\Binance\FuturesClient;
use Illuminate\Support\Collection;

class FuturesDealer
{
    protected FuturesClient $client;

    protected Collection $positions;

    protected array $long;

    protected array $short;

    public function __construct()
    {
        $this->client = new FuturesClient(
            config('services.binance.key'),
            config('services.binance.secret')
        );
        $this->positions = $this->client->positions();
        $this->long = $this->positions->get(0);
        $this->short = $this->positions->get(1);
    }

    public function isInactive(): bool
    {
        return false == $this->isActive();
    }

    public function isActive(): bool
    {
        return (float)$this->long['positionAmt'] && (float)$this->short['positionAmt'];
    }

    public function longPlan(): array
    {
        $plans = [];
        $steps = 6;
        $startSize = 0.02;
        $entry = $this->long['markPrice'] - 0.1;

        $plans[] = [
            'size' => $startSize,
            'entry' => (float)number_format($entry, 2),
            'total' => (float)number_format($startSize * $entry, 2)
        ];

        $planner = function ($_plans) {
            $limitMove = -0.0253339459778792;
            $_size = 0;
            $_entry = 0;

            foreach ($_plans as $plan) {
                $_size += $plan['size'];
            }

            foreach ($_plans as $plan) {
                $_entry += $plan['size'] * $plan['entry'] / $_size;
            }

            $output = [
                'size' => $_size * 2,
                'entry' => (float)number_format($_entry * (1 + $limitMove) + 0.02, 2)
            ];

            return [
                'size' => $output['size'],
                'entry' => $output['entry'],
                'total' => (float)number_format($output['size'] * $output['entry'], 2)
            ];
        };

        for ($i = 1; $i < $steps; $i++) {
            $plans[] = $planner($plans);
        }

        return $plans;
    }
}