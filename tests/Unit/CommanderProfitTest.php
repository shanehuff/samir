<?php

namespace Tests\Unit;

use App\Commander\InteractsWithCommanders;
use App\Models\Commander;
use App\Models\CommanderTrade;
use App\Commander\Profit;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CommanderProfitTest extends TestCase
{
    use DatabaseTransactions,
        InteractsWithCommanders;

    protected Commander $commander;

    /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
    public function setUp(): void
    {
        parent::setUp();

        $this->commander = $this->createCommander(
            'Test Commander', // name
            1000, // fund
            1, // 1% risk,
            0 // 3Commas bot ID
        );

        $this->seedTradingRecords();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_it_can_calculate_profit_for_simple_trades()
    {
        $profit = $this->commander->getProfit();

        $this->assertInstanceOf(Profit::class, $profit);
    }

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
