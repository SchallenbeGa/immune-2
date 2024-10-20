<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\SiteStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SiteController extends Controller
{
    // Affiche la page d'ajout de site et la liste des sites
    public function index()
    {
        $sites = Site::all();
        $offlineStatuses = SiteStatus::where('status', false)
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy(function($status) {
            return $status->created_at->format('Y-m-d'); // Grouper par date
        });
        return view('sites.index',['navbar_active'=>'analyse'], compact('sites','offlineStatuses'));
    }

    // Ajoute un site
    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'name' => 'required|string|max:255',
        ]);

        Site::create([
            'url' => $request->url,
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Site ajouté avec succès !');
    }

    // Vérifie le statut des sites
    public function checkSite(Site $site)
    {
        try {
            $response = Http::get($site->url);
            $status = $response->successful();
            $message = $status ? 'Le site est en ligne' : 'Le site est hors ligne';
        } catch (\Exception $e) {
            $status = false;
            $message = 'Erreur de connexion : ' . $e->getMessage();
        }

        // Enregistrer le statut
        $site->statuses()->create([
            'status' => $status,
            'message' => $message,
        ]);

        // Vérifier si le statut a changé
        if ($site->status != $status) {
            // Envoyer une alerte via ntfy.sh
            Http::post('https://ntfy.sh/jobAlert', [
                'topic' => 'Alerte de statut de site',
                'message' => $message . ' - ' . $site->url,
                'title' => 'Changement de statut de site',
            ]);

            // Mettre à jour le statut du site
            $site->update(['status' => $status]);
        }
    }
  
    // Affiche l'historique des vérifications pour un site
    public function show(Site $site)
    {
        $statuses = $site->statuses()->orderBy('created_at', 'desc')->get();
       
    
        return view('sites.show',['navbar_active'=>'analyse'], compact('site', 'statuses'));
    }
    public function captureScreenshot(Site $site)
{
    // Chemin du script Node.js
    $scriptPath = base_path('scripts/screenshot.js');

    // Générer un nom de fichier unique pour la capture d'écran
    $fileName = 'screenshot_' . $site->id . '_' . time() . '.png';

    // Chemin pour enregistrer l'image dans le répertoire 'public/screenshots'
    $outputPath = public_path('screenshots/' . $fileName);

    // Exécuter le script Node.js avec les paramètres (URL du site et chemin de sortie)
    $command = "/opt/alt/alt-nodejs20/root/usr/bin/node $scriptPath {$site->url} $outputPath";
    exec($command, $output, $return_var);

    if ($return_var === 0) {
        // Sauvegarder le chemin de la capture dans la base de données si nécessaire
        $site->screenshot_path = 'screenshots/' . $fileName;
        $site->save();

        return redirect()->back()->with('success', 'Capture d\'écran réalisée avec succès.');
    } else {
        return redirect()->back()->with('error', 'Erreur lors de la capture d\'écran.');
    }
}

}
