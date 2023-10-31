<?php

namespace App\Trading;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profit extends Model
{
    use HasFactory;

    protected $table = 'profits';

    protected $guarded = [];

    public function trade(): BelongsTo
    {
        return $this->belongsTo(Trade::class);
    }
}