<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeComputerHistory extends Model
{
    use HasFactory;
    protected $fillable = ['computer_id', 'employee_id', 'assigned_at'];

    public function computer()
    {
        return $this->belongsTo(Computer::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
