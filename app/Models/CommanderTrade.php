<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommanderTrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'commander_id',
        'bot_id',
        'side',
        'amount',
        'entry',
        'created_at', // for testing purpose
        'updated_at' // for testing purpose
    ];
}
