<?php

namespace Tests\Unit;

use App\Commander\InteractsWithCommanders;
use App\Models\Commander;
use App\Commander\Strategy;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CommanderStrategyTest extends TestCase
{
    use DatabaseTransactions,
        InteractsWithCommanders,
        InteractsWithTradingSeeds;

    protected Commander $commander;

    /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
    public function setUp(): void
    {
        parent::setUp();

        $this->commander = $this->createCommander(
            'Commander With Strategy',
            1000,
            1
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_it_can_create_a_new_strategy()
    {
        $strategy = $this->commander->getStrategy();

        $strategy->setBuyMode([
            'indicator' => 'stochastic',
            'value' => 'under 20',
            'resolution' => 240 // 4h timeframe
        ]);

        $strategy->setBuyTrigger([
            'indicator' => 'stochastic',
            'value' => 'under 10',
            'resolution' => 5 // 5m timeframe
        ]);

        $strategy->setSellMode([
            'indicator' => 'stochastic',
            'value' => 'above 80',
            'resolution' => 240 // 4h timeframe
        ]);

        $strategy->setSellTrigger([
            'indicator' => 'stochastic',
            'value' => 'above 90',
            'resolution' => 5 // 5m timeframe
        ]);

        $strategy->create();

        $this->assertInstanceOf(Strategy::class, $strategy);
    }
}
