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
        $logFilePath = storage_path('logs/laravel.log');
        $logs = file_exists($logFilePath) ? file($logFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

        return view('command_history.index', compact('histories', 'logs'));
    }

    public function show(CommandHistory $commandHistory)
    {
        return view('command_history.show', compact('commandHistory'));
    }
}
