<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectFile extends Model
{
    // Définir le nom de la table explicitement si elle ne suit pas la convention "snake_case plural"
    protected $table = 'project_files';

    // Les colonnes qui peuvent être remplies
    protected $fillable = [
        'file_name',
        'file_path',
        'file_size',
        'last_modified',
    ];

    // Si tu utilises une table avec `timestamps`, Laravel s'attend par défaut à ce que les colonnes soient
    // `created_at` et `updated_at`. Si elles sont différentes, tu peux les spécifier ici
    public $timestamps = true;
    public function analyses()
    {
        return $this->hasMany(FileRecommandation::class);
    }
    // Pour définir des relations ou d'autres comportements spécifiques (si nécessaire)
}
