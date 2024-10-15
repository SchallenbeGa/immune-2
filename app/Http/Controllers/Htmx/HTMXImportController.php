<?php

namespace App\Http\Controllers\Htmx;

use App\Support\Helpers;
use App\Models\Employee;
use App\Models\Computer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HTMXImportController extends Controller
{
    public function import(Request $request)
    {
  
        $jsonData = json_decode(file_get_contents($request->file('json_file')), true);
        
        foreach ($jsonData['pc_list'] as $pcData) {
            // Vérifier si l'utilisateur existe déjà, sinon le créer
            $employee = Employee::firstOrCreate(
                ['name' => $pcData['employee']]
            );

            // Vérifier si le PC existe déjà, sinon le créer ou le mettre à jour
            Computer::updateOrCreate(
                ['reference' => $pcData['pc']], // Critère pour identifier la machine
                ['employee_id' => $employee->id]        // Mise à jour des informations
            );
        }
        $existingReferences = Computer::pluck('reference')->toArray();

        $newReferences = array_map(function($pcData) {
            return $pcData['pc'];
        }, $jsonData['pc_list']);
        
        // Supprimer les PCs qui ne sont plus dans la nouvelle liste
        Computer::whereNotIn('reference', $newReferences)->delete();
        return view('home.partials.import', [
            'oob_swap' => true
        ])
        .view('home.partials.form-message', [
            'message' => 'Successfully updated.',
            'oob_swap' => true
        ])
        .view('components.navbar', ['navbar_active' => 'settings']);
    }
}
