<?php

namespace Tests\Unit;

use App\Commander\InteractsWithCommanders;
use App\Jobs\ProcessTradingviewAlert;
use App\Models\Commander;
use App\Models\TradingviewAlert;
use App\Tradingview\InteractsWithTradingviewAlerts;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
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

        Event::fake();

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

    public function test_it_can_switching_mode_of_commander_with_4h_alert()
    {

        /** @var TradingviewAlert $alert */
        $alert = $this->alert;

        /** @var Commander $commander */
        $commander = $this->commander;

        // Switch from chill to sell mode
        $this->assertEquals(Commander::STATUS_CHILL, $commander->status);
        (new ProcessTradingviewAlert($alert, $commander))->handle();
        $this->assertEquals(Commander::STATUS_SELL, $commander->status);

        // Switch from sell to chill mode
        $alert->side = 'chill';
        $alert->save();

        (new ProcessTradingviewAlert($alert, $commander))->handle();
        $this->assertEquals(Commander::STATUS_CHILL, $commander->status);

        // Switch from chill to buy mode
        $alert->side = 'buy';
        $alert->save();

        (new ProcessTradingviewAlert($alert, $commander))->handle();
        $this->assertEquals(Commander::STATUS_BUY, $commander->status);

        // Switch from buy to sell mode
        $alert->side = 'sell';
        $alert->save();

        (new ProcessTradingviewAlert($alert, $commander))->handle();
        $this->assertEquals(Commander::STATUS_SELL, $commander->status);

        // Switch from sell to buy mode
        $alert->side = 'buy';
        $alert->save();

        (new ProcessTradingviewAlert($alert, $commander))->handle();
        $this->assertEquals(Commander::STATUS_BUY, $commander->status);
    }

    public function test_it_command_a_bot_to_execute_a_sell_trade_from_5m_sell_signal()
    {
        /** @var TradingviewAlert $alert */
        $alert = $this->alert;
        $alert->timeframe = '5m';
        $alert->price = 9999;
        $alert->save();

        /** @var Commander $commander */
        $commander = $this->commander;

        // Do nothing if commander is chilling
        $this->assertEquals(Commander::STATUS_CHILL, $commander->status);
        (new ProcessTradingviewAlert($alert, $commander))->handle();
        $this->assertEquals(Commander::STATUS_CHILL, $commander->status);

        $this->assertDatabaseMissing('commander_trades', [
            'commander_id' => $commander->id,
            'bot_id' => $commander->bot_id,
            'side' => 'sell'
        ]);

        $commander->selling();
        // Creates selling trade
        $this->assertEquals(Commander::STATUS_SELL, $commander->status);
        (new ProcessTradingviewAlert($alert, $commander))->handle();
        $this->assertEquals(Commander::STATUS_SELL, $commander->status);

        $this->assertDatabaseHas('commander_trades', [
            'commander_id' => $commander->id,
            'bot_id' => $commander->bot_id,
            'side' => 'sell',
            'amount' => 10,
            'entry' => 9999
        ]);
    }

    public function test_it_command_a_bot_to_execute_a_buy_trade_from_5m_buy_signal()
    {
        /** @var TradingviewAlert $alert */
        $alert = $this->alert;
        $alert->timeframe = '5m';
        $alert->side = 'buy';
        $alert->price = 9999;
        $alert->save();

        /** @var Commander $commander */
        $commander = $this->commander;

        $commander->buying();
        // Creates buying trade
        $this->assertEquals(Commander::STATUS_BUY, $commander->status);
        (new ProcessTradingviewAlert($alert, $commander))->handle();
        $this->assertEquals(Commander::STATUS_BUY, $commander->status);

        $this->assertDatabaseHas('commander_trades', [
            'commander_id' => $commander->id,
            'bot_id' => $commander->bot_id,
            'side' => 'buy',
            'amount' => 10,
            'entry' => 9999
        ]);
    }
}
