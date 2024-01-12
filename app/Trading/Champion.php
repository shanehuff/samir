<?php

namespace App\Trading;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Champion extends Model
{
    public const ARCHETYPE_FARMER = 'farmer';

    protected $table = 'champions';

    protected $guarded = [];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}