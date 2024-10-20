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
        return view('command_history.index', compact('histories'));
    }

    public function show(CommandHistory $commandHistory)
    {
        return view('command_history.show', compact('commandHistory'));
    }
}
