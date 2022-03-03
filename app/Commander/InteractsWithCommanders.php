<?php

namespace App\Commander;

use App\Models\Commander;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait InteractsWithCommanders
{
    public function createCommander(string $name, int $fund, int $risk, int $botId, $status = Commander::STATUS_CHILL): Model|Builder
    {
        return Commander::query()->create([
            'name' => $name,
            'fund' => $fund,
            'risk' => $risk,
            'bot_id' => $botId,
            'status' => $status
        ]);
    }
}
