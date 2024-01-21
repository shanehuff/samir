<?php

namespace App\Trading;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpotOrder extends Model
{
    protected $primaryKey = 'order_id';

    public $incrementing = false;

    const STATUS_NEW = 0;
    const STATUS_FILLED = 1;
    const STATUS_CLOSED = 2;
    const STATUS_EXPIRED = 3;
    const SIDE_SELL = 'SELL';
    const SIDE_BUY = 'BUY';

    const STATUS = [
        'NEW' => self::STATUS_NEW,
        'FILLED' => self::STATUS_FILLED,
        'CANCELED' => self::STATUS_CLOSED,
        'EXPIRED' => self::STATUS_EXPIRED
    ];

    protected $table = 'spot_orders';

    protected $guarded = [];

    public function champion(): BelongsTo
    {
        return $this->belongsTo(Champion::class);
    }

    public function trades(): HasMany
    {
        return $this->hasMany(SpotTrade::class, 'order_id');
    }
}