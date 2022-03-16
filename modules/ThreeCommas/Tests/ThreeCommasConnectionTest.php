<?php

namespace Modules\ThreeCommas\Tests;

use Tests\TestCase;

class ThreeCommasConnectionTest extends TestCase
{
    public function test_ping_to_3commas_Api_successfully(): void
    {
        $response = $this->get(route('three_commas.ping'));
        $this->assertEquals($response['data']['pong'], 'pong');
    }
}

