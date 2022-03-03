<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ApiTradingviewTokenTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Config::set('tradingview.secret', 'test-secret');
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_it_can_not_access_tradingview_endpoint_without_secret_token()
    {
        $response = $this->postJson('/api/tradingview');

        $response->assertStatus(401);
    }

    public function test_it_can_not_access_tradingview_endpoint_with_a_wrong_secret_token()
    {
        $response = $this->postJson('/api/tradingview', [
            'secret' => 'wrong-secret'
        ]);

        $response->assertStatus(401);
    }

    public function test_it_can_access_tradingview_endpoint_correctly()
    {
        $response = $this->postJson('/api/tradingview', [
            'secret' => 'test-secret',
            'payloads' => [
                'side' => 'buy',
                'timeframe' => '5m'
            ]
        ]);

        $response->assertStatus(200);
    }


}
