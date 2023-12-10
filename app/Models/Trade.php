<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Trade extends Model
{
    
    protected $fillable = [
        'price',
        'side',
        'symbol_id',
    ];

    public function getRouteKeyName(): string
    {
        return 'name';
    }
    /**
     * Symbol mov.
     */
    /**
     * Get user written articles.
     */
    public function symbol(): BelongsTo
    {
        return $this->belongsTo(Symbol::class);
    }
}
