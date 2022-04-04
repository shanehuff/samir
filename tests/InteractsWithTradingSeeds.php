<?php

namespace Tests;

trait InteractsWithTradingSeeds
{
    private function seedTradingRecords() {
        $commander = $this->commander;

        CommanderTrade::create([
            'side' => 'buy',
            'bot_id' => $commander->bot_id,
            'commander_id' => $commander->id,
            'amount' => 420.00,
            'entry' => 420.00
        ]);

        CommanderTrade::create([
            'side' => 'sell',
            'bot_id' => $commander->bot_id,
            'commander_id' => $commander->id,
            'amount' => 450.00,
            'entry' => 450.00
        ]);
    }
}
