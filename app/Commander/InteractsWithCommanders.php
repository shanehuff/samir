<?php

namespace App\Commander;

use App\Models\Commander;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

    public function findOrCreateCommander(string $name, int $fund, int $risk, int $botId): Model|Builder
    {
        $commander = Commander::query()
            ->where('name', $name)
            ->where('fund', $fund)
            ->where('risk', $risk)
            ->where('bot_id', $botId)
            ->first();


        return $commander ?? $this->createCommander($name, $fund, $risk, $botId);

    }
}
