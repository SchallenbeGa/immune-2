<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ohlvc extends Model
{
    
    protected $fillable = [
        'symbol_id',
        'open',
        'high',
        'low',
        'close',
        'volume',
        'slug',
    ];
    /**
     * Ohlvc symbol.
     */
    public function symbol(): BelongsTo
    {
        return $this->belongsTo(Symbol::class);
    }
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
