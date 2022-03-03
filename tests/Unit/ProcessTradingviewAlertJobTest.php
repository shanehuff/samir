<?php

namespace Tests\Unit;

use App\Commander\InteractsWithCommanders;
use App\Jobs\ProcessTradingviewAlert;
use App\Models\Commander;
use App\Models\TradingviewAlert;
use App\Tradingview\InteractsWithTradingviewAlerts;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProcessTradingviewAlertJobTest extends TestCase
{
    use InteractsWithCommanders,
        InteractsWithTradingviewAlerts,
        DatabaseTransactions;

    public Model $alert;

    public Model $commander;

    public function setUp(): void
    {
        parent::setUp();

        $this->alert = $this->createTradingviewAlert(
            'sell',
            '4h'
        );

        $this->commander = $this->createCommander(
            '4h 1k 1%',
            1000,
            1,
            123456
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_it_can_turn_on_sell_mode_of_commander_with_4h_alert()
    {
        /** @var TradingviewAlert $alert */
        $alert = $this->alert;

        /** @var Commander $commander */
        $commander = $this->commander;

        $this->assertEquals(Commander::STATUS_CHILL, $commander->status);
        (new ProcessTradingviewAlert($alert, $commander))->handle();
        $this->assertEquals(Commander::STATUS_SELL, $commander->status);
    }
}
