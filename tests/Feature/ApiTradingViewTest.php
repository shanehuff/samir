<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTradingViewTest extends TestCase
{
    public function test_it_can_not_access_tradingview_endpoint_without_secret_token()
    {
        $response = $this->postJson('/api/tradingview');

        $response->assertStatus(401);
    }

    public function test_it_can_not_access_tradingview_endpoint_with_a_wrong_secret_token()
    {
        $response = $this->postJson('/api/tradingview');

        $response->assertStatus(401);
    }
}
