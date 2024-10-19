<?php
namespace App\Http\Controllers;

use App\Models\FileRecommandation;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\ProjectFile;

class AnalysisController extends Controller
{
    public function index()
    {
        // Récupérer les dates des analyses, groupées par jour
        $dates = FileRecommandation::selectRaw('DATE(created_at) as date')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return view('analyses.index',[ 'navbar_active' => 'analyse'], compact('dates'));
    }
    public function showStructure()
    {
        // Récupérer tous les fichiers dans la table project_files
        $files = ProjectFile::all()->groupBy(function ($file) {
            return dirname($file->file_path); // Récupérer le répertoire parent
        });

        // Retourner la vue avec la liste des fichiers organisés par dossier
        return view('analyses.structure',[ 'navbar_active' => 'analyse'], compact('files'));
    }
    public function showByDate($date)
    {
        // Récupérer les analyses pour une date spécifique
        $analyses = FileRecommandation::whereDate('created_at', $date)->get();
        $analyses = $analyses->map(function($analysis) {
            $analysis->file_name = basename($analysis->file_path); // Extraire uniquement le nom du fichier
            return $analysis;
        });
        return response()->json($analyses);
    }
    // Récupérer les analyses pour un fichier donné
    public function showAnalyses($fileId)
    {
        $file = ProjectFile::with('analyses')->findOrFail($fileId); // Récupérer le fichier avec ses analyses
        return view('analyses.file_analyses',[ 'navbar_active' => 'analyse'], compact('file'));
    }
}
