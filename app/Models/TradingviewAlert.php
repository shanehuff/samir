<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingviewAlert extends Model
{
    use HasFactory;

    protected $table = 'tradingview_alerts';

    // Statuses
    public const STATUS_PENDING = 0;
    public const STATUS_PROCESSED = 1;

    protected $attributes = [
        'status' => self::STATUS_PENDING
    ];

    protected $fillable = [
        'side',
        'timeframe',
        'status'
    ];
}
