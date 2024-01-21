<?php

namespace App\Trading;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Champion extends Model
{
    public const ARCHETYPE_FARMER = 'farmer';

    protected $table = 'champions';

    protected $guarded = [];

    protected $casts = [
        'entry' => 'float',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function spotOrders(): HasMany
    {
        return $this->hasMany(SpotOrder::class);
    }

    public function getFundingIncomeAttribute()
    {
        return Income::query()
            ->where('time', '>=', $this->orders()->first()->update_time)
            ->where('symbol', '=', $this->symbol)
            ->get()
            ->sum('income');
    }

    public function getCurrentCapitalAttribute()
    {
        return $this->capital + $this->profit + $this->income - $this->fee;
    }

    public function getCanTradeAttribute(): bool
    {
        return 'active' === $this->status && $this->current_capital - $this->onduty > $this->grind;
    }
}