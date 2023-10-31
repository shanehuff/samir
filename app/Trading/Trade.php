<?php

namespace App\Trading;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trade extends Model
{
    use HasFactory;

    protected $table = 'trades';

    protected $guarded = [];

    public function profits(): HasMany
    {
        return $this->hasMany(Profit::class);
    }
}