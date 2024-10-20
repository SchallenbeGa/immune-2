<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Artisan; // Importer Artisan pour appeler les commandes
use App\Models\CrudOperation; // Importer le modèle

class CrudController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
        ]);

        // Récupérer la description depuis la requête
        $description = $request->input('description');

        // Appeler la commande artisan immu avec la description
        Artisan::call('immu', ['description' => $description]);

        // Optionnel : Récupérer les informations de la dernière opération enregistrée
        $crudOperation = CrudOperation::latest()->first();

        // Rediriger avec un message de succès
        return redirect()->back()->with('success', 'CRUD operations generated successfully!')->with('operation', $crudOperation);
    }
}
