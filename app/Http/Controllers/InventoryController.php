<?php

namespace App\Http\Controllers;

use App\Models\Computer;

class InventoryController extends Controller
{
    public function index()
    {
        // Récupérer toutes les machines avec les utilisateurs associés
        $computers = Computer::with('employee')->get();

        // Passer les données à la vue
        return view('inventory.index', compact('computers'));
    }
    public function showByReference($reference)
    {
        // Rechercher le PC par sa référence avec l'utilisateur associé
        $computer = Computer::with('employee')->where('reference', $reference)->firstOrFail();
        $computer = $computer->append('detailUrl');
        return view('inventory.show', compact('computer'));
    }
    public function getComputers()
    {
        // Récupérer toutes les machines avec les utilisateurs associés
        $computers = Computer::with('employee')->get();
        $computers = $computers->append('detailUrl');

        // Retourner les données sous forme de JSON
        return response()->json($computers);
    }
}
