<?php

namespace App\Dealers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerOrder extends Model
{
    use HasFactory;

    const STATUS_NEW = 0;
    const STATUS_FILLED = 1;
    const STATUS_CLOSED = 2;

    const STATUS = [
        'NEW' => self::STATUS_NEW,
        'FILLED' => self::STATUS_FILLED,
        'CANCELED' => self::STATUS_CLOSED,
        'EXPIRED' => self::STATUS_CLOSED
    ];

    protected $table = 'dealer_orders';

    protected $guarded = [];

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }
}