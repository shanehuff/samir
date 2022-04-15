<?php

namespace Tests\Feature;

use App\Commander\InteractsWithCommanders;
use App\Models\Commander;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Tests\InteractsWithTradingSeeds;

class ApiGetCommanderRiskTest extends TestCase
{
    use DatabaseTransactions,
        WithoutMiddleware,
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

    public function test_it_shows_single_commander_risk_correctly()
    {
        $response = $this->json('GET', sprintf('/api/commanders/%s/risk', $this->commander->id));

        $response->assertOK();
        $response->assertJson([
            'commander_id' => $this->commander->id,
            'leverage' => 20,
            'balance' => 33.0773,
            'liquidation_price' => 165.6
        ]);
    }
}
