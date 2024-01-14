<?php

namespace App\Trading;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Trade extends Model
{
    use HasFactory;

    protected $table = 'trades';

    protected $guarded = [];

    public function profits(): HasMany
    {
        return $this->hasMany(Profit::class);
    }

    public function counterTrade(): object|null
    {
        return self::query()
            ->where('symbol', $this->symbol)
            ->where('side', '=', $this->side === Order::SIDE_BUY ? Order::SIDE_SELL : Order::SIDE_BUY)
            ->where('position_side', '=', $this->position_side)
            ->where('id', '<', $this->id)
            ->orderByDesc('id')
            ->first();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}