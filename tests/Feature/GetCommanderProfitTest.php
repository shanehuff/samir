<?php

namespace Tests\Feature;

use App\Commander\InteractsWithCommanders;
use App\Models\Commander;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Tests\InteractsWithTradingSeeds;

class GetCommanderProfitTest extends TestCase
{
    use DatabaseTransactions,
        WithoutMiddleware,
        InteractsWithCommanders,
        InteractsWithTradingSeeds;

    protected Commander $commander;

    public function setUp(): void
    {
        parent::setUp();

        $this->commander = $this->createCommander(
            'Test Commander', // name
            1000, // fund
            1, // 1% risk,
            0 // 3Commas bot ID
        );

        $this->
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_it_shows_single_commander_profit_correctly()
    {
        $response = $this->json(
            sprintf('/api/commanders/%s/profit', $this->commander->id)
        );

        $response->assertOK();
        $response->assertJson([
            'commander_id' => $this->commander->id,
            'profit' => 30.0
        ]);
    }
}
