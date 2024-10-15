<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Computer extends Model
{
    use HasFactory;

    protected $fillable = ['reference', 'employee_id'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function getDetailUrlAttribute()
    {
        // Retourne l'URL de détail basée sur l'ID de l'ordinateur
        return url("/computers/{$this->reference}");
    }
}
