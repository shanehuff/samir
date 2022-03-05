<?php

namespace App\Listeners;

use App\Commander\InteractsWithCommanders;
use App\Events\TradingviewAlertCreated;
use App\Jobs\ProcessTradingviewAlert;
use App\Models\Commander;
use Illuminate\Database\Eloquent\Model;

class UpdateCommanderStatusUsingTradingviewAlert
{
    use InteractsWithCommanders;

    public Model|Builder|Commander $commander;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->commander = $this->findOrCreateCommander(
            'Megan Shi',
            1000,
            1,
            123456
        );
    }

    /**
     * Handle the event.
     *
     * @param TradingviewAlertCreated $event
     * @return void
     */
    public function handle(TradingviewAlertCreated $event)
    {
        ray('ok');
        dispatch(new ProcessTradingviewAlert($event->alert, $this->commander));
    }
}
