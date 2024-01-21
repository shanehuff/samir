<?php

namespace App\Trading;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpotTrade extends Model
{
    protected $table = 'spot_trades';

    protected $guarded = [];

    protected $casts = [
        'commission' => 'float',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(SpotOrder::class, 'order_id');
    }
}