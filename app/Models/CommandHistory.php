<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommandHistory extends Model
{
    use HasFactory;
     // Spécifiez les champs que vous souhaitez remplir
     protected $fillable = [
        'command',
        'success',
        'output',
        'created_at',
    ];

    // Optionnel : Définissez les dates
    protected $dates = [
        'created_at',
    ];

    // Optionnel : Définissez un attribut pour afficher le statut
    public function getStatusAttribute()
    {
        return $this->success ? 'Réussie' : 'Échouée';
    }
}
