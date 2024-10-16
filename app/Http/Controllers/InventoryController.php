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
        $computer = Computer::with(['employee','employeeHistory.employee'])->where('reference', $reference)->firstOrFail();
        $computer_history = Computer::withTrashed()->where('reference', $reference)->firstOrFail();
        $computer = $computer->append('detailUrl');
        $computer->employeeHistory->pop();
        $employee_history = $computer->employeeHistory->map(function ($history) {
            return [
                'employee' => $history->employee->name,
                'assigned_at' => $history->assigned_at,
            ];
        })->sortDesc();
        
        return view('inventory.show', compact('computer','employee_history'));
    }
    public function showByReferenceJson($reference)
    {
        // Rechercher le PC par sa référence avec l'utilisateur associé
        $computer = Computer::with(['employee','userHistory.user'])->where('reference', $reference)->firstOrFail();
        dd($computer);
        $computer = $computer->append('detailUrl');
        return response()->json($computer);
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
