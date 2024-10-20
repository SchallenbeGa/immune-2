<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;

class VisitController extends Controller
{
    /**
     * Afficher la liste des visites.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Récupérer toutes les visites de la base de données
        $visits = Visit::orderBy('created_at', 'desc')->get();

        // Retourner la vue avec les visites
        return view('visits.index', compact('visits'));
    }
}
