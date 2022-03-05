<?php

namespace App\Events;

use App\Models\TradingviewAlert;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TradingviewAlertCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public TradingviewAlert $alert;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(TradingviewAlert $alert)
    {
        $this->alert = $alert;
    }
}
