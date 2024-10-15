<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Computer;

class ImportController extends Controller
{
    public function show($id)
    {
        // Trouver le PC avec l'utilisateur associé
        $computer = Computer::with('employee')->findOrFail($id);

        // Retourner la vue avec les détails du PC
        return view('inventory.show', compact('computer'));
    }
    public function import(Request $request)
    {
        // Assumons que le fichier JSON est uploadé ou présent localement
        $jsonData = json_decode(file_get_contents($request->file('json_file')), true);
        
        foreach ($jsonData['pc_list'] as $pcData) {
            // Vérifier si l'utilisateur existe déjà, sinon le créer
            $user = Employee::firstOrCreate(
                ['name' => $pcData['employee']]
            );

            // Vérifier si le PC existe déjà, sinon le créer ou le mettre à jour
            Computer::updateOrCreate(
                ['reference' => $pcData['pc']], // Critère pour identifier la machine
                ['employee_id' => $user->id]        // Mise à jour des informations
            );
        }

        return response()->json(['message' => 'Données importées et mises à jour avec succès']);
    }
}
