<?php

namespace App\Dealers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerOrder extends Model
{
    use HasFactory;

    const STATUS_NEW = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_CLOSED = 2;

    protected $table = 'dealer_orders';

    protected $guarded = [];

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }
}