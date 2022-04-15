<?php

namespace Tests\Unit;

use App\Commander\InteractsWithCommanders;
use App\Models\Commander;
use App\Commander\Risk;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\InteractsWithTradingSeeds;

class CommanderRiskTest extends TestCase
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
            'Test Commander', // name
            33.0773, // fund
            1, // 1% risk,
            0 // 3Commas bot ID
        );

        $this->seedTradingRecords('for_risk_calculating');
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_it_can_calculate_risk_for_simple_trades()
    {
        $risk = $this->commander->getRisk();

        ray($this->commander);

        $this->assertInstanceOf(Risk::class, $risk);
        $this->assertEquals(20, $risk->getLeverage());
        $this->assertEquals(2.70, round($risk->getMargin(), 2));
        $this->assertEquals(30.37, round($risk->getAvailableMargin(), 2));
        $this->assertEquals(165.60, round($risk->getLiquidationPrice(), 2));
    }
}
