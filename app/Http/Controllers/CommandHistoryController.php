<?php

namespace App\Http\Controllers;

use App\Models\CommandHistory;
use Illuminate\Http\Request;
use Artisan;

class CommandHistoryController extends Controller
{
    public function index()
    {
        // Récupérer l'historique des commandes
        $histories = CommandHistory::orderBy('created_at', 'desc')->paginate(10);
       // Lire les 100 dernières lignes des logs
        // Lire les 100 dernières lignes des logs
        $logFilePath = storage_path('logs/laravel.log');
        $logs = [];

        if (file_exists($logFilePath)) {
            // Lire toutes les lignes du fichier
            $allLogs = file($logFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            // Obtenir les 100 dernières lignes
            $logs = array_slice($allLogs, -100);
        }


        return view('command_history.index', compact('histories', 'logs'));
    }
    public function pullUpdate()
    {
        // Exécuter la commande artisan 'git:pull-update'
        Artisan::call('git:pull-update');

        // Retourner une réponse après exécution
        return redirect()->back()->with('success', 'Mise à jour Git effectuée avec succès.');
    }
    public function show(CommandHistory $commandHistory)
    {
        return view('command_history.show', compact('commandHistory'));
    }
}
