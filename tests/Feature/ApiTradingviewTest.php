<?php

namespace Tests\Feature;

use App\Models\TradingviewAlert;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Event;
use App\Events\TradingviewAlertCreated;
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
        Event::fake();

        $response = $this->postJson('/api/tradingview', [
            'payloads' => [
                'side' => 'buy',
                'timeframe' => '5m',
                'price' => 9999
            ]
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('tradingview_alerts', [
            'side' => 'buy',
            'timeframe' => '5m',
            'status' => TradingviewAlert::STATUS_PENDING,
            'price' => 9999
        ]);

        Event::assertDispatched(TradingviewAlertCreated::class);
    }

    public function test_it_store_tradingview_webhooks_to_db_v2_correctly()
    {
        Event::fake();

        $response = $this->postJson('/api/tradingview', [
            'payloads' => [
                'stochastic' => 90,
                'resolution' => 5,
                'price' => 9999
            ]
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('tradingview_alerts', [
            'stochastic' => 90,
            'resolution' => 5,
            'status' => TradingviewAlert::STATUS_PENDING,
            'price' => 9999
        ]);

        Event::assertDispatched(TradingviewAlertCreated::class);
    }
}
