<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commander extends Model
{
    use HasFactory;

    protected $table = 'commanders';

    // Statuses
    public const STATUS_CHILL = 0;
    public const STATUS_BUY = 1;
    public const STATUS_SELL = 2;

    protected $attributes = [
        'status' => self::STATUS_CHILL
    ];

    protected $fillable = [
        'name',
        'fund',
        'risk',
        'bot_id',
        'status'
    ];
}
