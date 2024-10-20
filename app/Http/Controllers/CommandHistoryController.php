<?php

namespace App\Http\Controllers;

use App\Models\CommandHistory;
use Illuminate\Http\Request;

class CommandHistoryController extends Controller
{
    public function index()
    {
        // Récupérer l'historique des commandes
        $histories = CommandHistory::orderBy('created_at', 'desc')->paginate(10);
       // Lire les 100 dernières lignes des logs
       $logFilePath = storage_path('logs/laravel.log');
       $logs = [];

       if (file_exists($logFilePath)) {
           // Lire le fichier de log et récupérer les 100 dernières lignes
           $file = new \SplFileObject($logFilePath);
           $file->seek(PHP_INT_MAX); // Aller à la fin du fichier

           $lineCount = 0;

           while ($file->key() >= 0 && $lineCount < 100) {
               $logs[] = $file->current();
               $file->prev();
               $lineCount++;
           }

           $logs = array_reverse($logs); // Inverser pour avoir les lignes les plus anciennes en haut
       }

        return view('command_history.index', compact('histories', 'logs'));
    }

    public function show(CommandHistory $commandHistory)
    {
        return view('command_history.show', compact('commandHistory'));
    }
}
