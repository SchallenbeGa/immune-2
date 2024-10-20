<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;
use Carbon\Carbon;
use App\Models\ProjectFile;
use App\Models\FileRecommandation;

class ExploreLaravelFiles extends Command
{
    protected $signature = 'explore:files';
    protected $description = 'Parcourt les fichiers principaux du projet Laravel et les analyse';

    public function handle()
    {
        $this->scanAndUpdateStructure();
    
    }
    public function scanAndUpdateStructure()
    {
        // Répertoires que tu veux scanner
        $directories = [
            app_path(), // App folder: Controllers, Models
            resource_path('views'), // Views
            base_path('routes'), // Routes
            base_path('database/migrations'), // Migrations
        ];

        foreach ($directories as $directory) {
            $this->scanDirectory($directory);
            $files = shell_exec("find $directory -type f");

            // Convertir la sortie shell en un tableau
            $fileArray = explode(PHP_EOL, trim($files));

           
        }

        return "Structure mise à jour avec succès.";
    }

    private function scanDirectory($directory)
    {
        // Parcourir tous les fichiers dans le répertoire donné
        $files = File::allFiles($directory);
        foreach ($files as $file) {
          
            // Obtenir les informations du fichier
            $filePath = $file->getPathname();  // Chemin complet du fichier
            $fileName = $file->getFilename();  // Nom du fichier
            $fileSize = $file->getSize();      // Taille du fichier
            $lastModified = Carbon::createFromTimestamp($file->getMTime()); // Dernière modification

            // Vérifier si le fichier existe déjà dans la base de données
            $existingFile = ProjectFile::where('file_path', $filePath)->first();

            if ($existingFile) {
                // Mettre à jour les informations si le fichier a changé
                $existingFile->update([
                    'file_size' => $fileSize,
                    'last_modified' => $lastModified,
                ]);
            } else {
                // Ajouter un nouveau fichier dans la base de données
                $p = ProjectFile::create([
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'file_size' => $fileSize,
                    'last_modified' => $lastModified,
                ]);
            }
            $this->processFile($file,$p->id);
        }
    }
    public function processFile($file,$id)
    {
            // Lire le contenu du fichier
             $this->info($file);
            $content = file_get_contents($file);

            if (!empty($content)) {
                // Vérifier si des recommandations existent déjà pour ce fichier et sont récentes
            $recentAnalysis = FileRecommandation::where('file_path', $file)
            ->where('created_at', '>=', now()->subHours(24)) // Dernières 24 heures
            ->exists();

        if ($recentAnalysis) {
            $this->info("Analyse récente trouvée pour le fichier : " . $file . ". Aucune nouvelle analyse effectuée.");
            return; // Ne pas analyser si une analyse récente existe
        }
                // Découper le fichier en morceaux de 1000 caractères
                $chunks = str_split($content, 1000);

                foreach ($chunks as $index => $chunk) {
                    // Générer des recommandations pour chaque morceau via l'API ChatGPT
                    $recommendation = $this->generateRecommendation($chunk);
                    $this->info($index);
                    // Enregistrer les recommandations dans la base de données
                    FileRecommandation::create([
                        'project_file_id' =>$id,
                        'file_path' => $file . " (partie ".$index.")",
                        'action_performed' => 'Analyse du contenu du fichier ('.$index.')',
                        'recommendation' => $recommendation,
                    ]);

                    $this->info("Partie $index du fichier analysée et recommandée : " . $file);
                }
            }
    }

    public function generateRecommendation($content)
    {
        $apiKey = '2';
        $url = "http://localhost:8080/v1/chat/completions";

        $prompt = "Voici un fichier de code Laravel :\n" . $content . "\nQuels sont les points d'amélioration possibles dans ce fichier ? (maximum 2 phrases)";

        // Structure de la requête
        $data = [
            "model" => "gpt-4", // ou "gpt-3.5-turbo" selon ce que tu utilises
            "messages" => [
                ["role" => "system", "content" => "Tu es un assistant expert en Laravel."],
                ["role" => "user", "content" => $prompt],
            ],
            "max_tokens" => 300
        ];

        // Configuration de la requête
        $options = [
            'http' => [
                'header'  => "Content-type: application/json\r\n" .
                             "Authorization: Bearer " . $apiKey . "\r\n",
                'method'  => 'POST',
                'content' => json_encode($data),
            ]
        ];

        // Effectuer la requête HTTP
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $response = json_decode($result, true);

        // Extraire et retourner la recommandation
        if (isset($response['choices'][0]['message']['content'])) {
            return $response['choices'][0]['message']['content'];
        }

        return "Aucune recommandation disponible pour ce fichier.";
    }
}
