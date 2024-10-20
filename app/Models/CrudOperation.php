<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrudOperation extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'migration_name',
        'model_name',
        'controller_name',
        'view_name',
    ];
}
