<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\CommandHistory;

class GitPullUpdate extends Command
{
    protected $signature = 'git:pull-update';
    protected $description = 'Effectue un git pull pour mettre à jour le site';

    public function handle()
    {
        // Définir le chemin du répertoire de votre projet
        $repositoryPath = base_path(); // Remplacez par le chemin approprié si nécessaire

        // Changer de répertoire
        chdir($repositoryPath);

        // Exécuter la commande git pull
        exec('git pull origin main', $output, $returnVar);

        // Créer l'historique de la commande
        $success = $returnVar === 0;
        CommandHistory::create([
            'command' => 'git pull origin main',
            'output' => implode("\n", $output),
            'success' => $success,
        ]);

        // Vérifiez si la commande a réussi
        if ($success) {
            Log::info('Git pull effectué avec succès : ' . implode("\n", $output));
            $this->info('Mise à jour réussie.');
        } else {
            Log::error('Échec du git pull : ' . implode("\n", $output));
            $this->error('Échec de la mise à jour.');
        }
    }
}
