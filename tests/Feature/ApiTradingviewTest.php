<?php

namespace Tests\Feature;

use App\Models\TradingviewAlert;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class ApiTradingviewTest extends TestCase
{
    use DatabaseTransactions,
        WithoutMiddleware;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_it_store_tradingview_webhooks_to_db_correctly()
    {
        $response = $this->postJson('/api/tradingview', [
            'payloads' => [
                'side' => 'buy',
                'timeframe' => '5m'
            ]
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('tradingview_alerts', [
            'side' => 'buy',
            'timeframe' => '5m',
            'status' => TradingviewAlert::STATUS_PENDING
        ]);
    }
}
