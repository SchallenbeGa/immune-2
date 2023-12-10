<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Signal extends Model
{
    use HasFactory;
    
    protected $fillable = [
        '',
    ];

    public function getRouteKeyName(): string
    {
        return 'msg';
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
