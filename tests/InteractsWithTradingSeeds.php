<?php

namespace Tests;

use App\Models\CommanderTrade;

trait InteractsWithTradingSeeds
{
    private function seedTradingRecords($case = '') {
        switch($case) {
            case 'for_risk_calculating':
                $this->setUpTradesForRiskCalculating();
                break;
            default:
                $this->setUpDefaultTrades();
        }
    }

    public function setUpTradesForRiskCalculating()
    {
        $commander = $this->commander;

        CommanderTrade::create([
            'side' => 'buy',
            'bot_id' => $commander->bot_id,
            'commander_id' => $commander->id,
            'amount' => 0.13*415.904,
            'entry' => 415.904,
            'created_at' => '2022-04-01 00:00:00',
            'updated_at' => '2022-04-01 00:00:00'
        ]);
    }

    public function setUpDefaultTrades()
    {
        $commander = $this->commander;

        CommanderTrade::create([
            'side' => 'buy',
            'bot_id' => $commander->bot_id,
            'commander_id' => $commander->id,
            'amount' => 420.00,
            'entry' => 420.00,
            'created_at' => '2022-04-01 00:00:00',
            'updated_at' => '2022-04-01 00:00:00'
        ]);

        CommanderTrade::create([
            'side' => 'sell',
            'bot_id' => $commander->bot_id,
            'commander_id' => $commander->id,
            'amount' => 450.00,
            'entry' => 450.00,
            'created_at' => '2022-05-01 00:00:00',
            'updated_at' => '2022-05-01 00:00:00'
        ]);
    }
}
