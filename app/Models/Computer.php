<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class Computer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'localisation',
        'garantie',
        'date_achat',
        'date_fin_garantie',
        'reference',
        'employee_id'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function getDetailUrlAttribute()
    {
        // Retourne l'URL de détail basée sur l'ID de l'ordinateur
        return url("/computers/{$this->reference}");
    }
    public function employeeHistory()
{
    return $this->hasMany(EmployeeComputerHistory::class);
}

}
