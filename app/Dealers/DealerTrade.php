<?php

namespace App\Dealers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerTrade extends Model
{
    use HasFactory;

    protected $table = 'dealer_trades';

    protected $guarded = [];

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }
}