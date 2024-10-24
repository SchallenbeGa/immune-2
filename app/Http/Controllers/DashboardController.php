<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord avec les graphiques des visites.
     */
    public function index(Request $request)
    {
        // Filtrage
        $browser = $request->input('browser');
        $os = $request->input('os');
        $country = $request->input('country');
        $search = $request->input('search');

        // Construire la requête avec filtres
        $query = Visit::query();

        if ($browser) {
            $query->whereRaw('user_agent LIKE ?', ["%{$browser}%"]);
        }

        if ($os) {
            $query->whereRaw('user_agent LIKE ?', ["%{$os}%"]);
        }

        if ($country) {
            $query->where('country', $country);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('ip_address', 'LIKE', "%{$search}%")
                  ->orWhere('url', 'LIKE', "%{$search}%");
            });
        }

        // Récupérer les visites filtrées
        $visits = $query->orderBy('created_at', 'desc')->paginate(10);

        // Nombre de visites par navigateur, pays, et date pour les graphiques
        $visitsByBrowser = Visit::selectRaw('user_agent, COUNT(*) as count')->groupBy('user_agent')->get();
        $visitsByCountry = Visit::selectRaw('country, COUNT(*) as count')->groupBy('country')->get();
        $visitsByDate = Visit::selectRaw('DATE(created_at) as date, COUNT(*) as count')->groupBy('date')->orderBy('date', 'asc')->get();

        return view('dashboard.index',['navbar_active'=>'analyse'], compact('visits', 'visitsByBrowser', 'visitsByCountry', 'visitsByDate', 'browser', 'os', 'country', 'search'));
    }
}
