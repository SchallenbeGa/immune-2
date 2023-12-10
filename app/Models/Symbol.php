<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Symbol extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
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
    public function data()
    {
        return $this->hasMany(Ohlvc::class, 'symbol_id');
    }
    public function data_s()
    {
        return $this->hasMany(Signal::class, 'symbol_id');
    }
    public function data_t()
    {
        return $this->hasMany(Trade::class, 'symbol_id');
    }
}
