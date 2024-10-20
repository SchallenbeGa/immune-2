<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use GuzzleHttp\Client;
use App\Models\CrudOperation; // Ajouter le modèle

class GenerateCRUDCommand extends Command
{
    protected $signature = 'immu {description}';
    protected $description = 'Generate CRUD operations based on the provided description';

    public function handle()
    {
        $description = $this->argument('description');

        // Étape 1 : Demande à ChatGPT de générer une migration, un modèle, un contrôleur, et une vue
        $code = $this->generateCodeFromAI($description);

        // Étape 2 : Écrire le fichier de migration
        $migrationPath = database_path('migrations');
        $migrationFileName = $this->createMigrationFile($code['migration'], $code['migration_name']);
        
        // Étape 3 : Créer le modèle
        $this->createModel($code['model'], $code['model_name']);

        // Étape 4 : Créer le contrôleur
        $this->createController($code['controller'], $code['controller_name']);

        // Étape 5 : Créer la route
        $this->createRoute($code['route']);

        // Étape 6 : Créer la vue
        $this->createView($code['view'], $code['view_name']);

        // Étape 7 : Exécuter les migrations
        $this->call('migrate');

        // Étape 8 : Enregistrer les informations dans la base de données
        $this->saveOperationToDatabase($description, $code);

        $this->info('CRUD operations generated successfully!');
    }

    private function saveOperationToDatabase($description, $code)
    {
        CrudOperation::create([
            'description' => $description,
            'migration_name' => $code['migration_name'],
            'model_name' => $code['model_name'],
            'controller_name' => $code['controller_name'],
            'view_name' => $code['view_name'],
        ]);
    }

    private function generateCodeFromAI($description)
    {
        // Configurer le client HTTP pour interagir avec l'API ChatGPT
        $client = new Client();
        $response = $client->post('http://localhost:8080/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . '2',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'user', 'content' => "Peux-tu générer du code PHP pour une application Laravel $description qui inclut les éléments suivants :

1. le code contenu dans la migration. (migration)
2. Un nom pour la migration. (migration_name)
3. le code contenu dans le fichier model. (model)
4. Un nom pour le model. (model_name)
5. le code contenu dans le fichier controller. (controller)
6. Un nom pour le controller. (controller_name)
7. le code a ajouté dans le fichier route. (route)
8. le code contenu dans le fichier view. (view)
9. Un nom pour la vue. (view_name)
Assure-toi de donner des noms pertinents. Veuillez renvoyer le code formaté en json."],
                ],
            ],
        ]);

        $result = json_decode($response->getBody(), true);
        dd($result);
        return $this->parseAIResponse($result);
    }

    private function parseAIResponse($response)
    {
        
        // Supposer que l'IA renvoie une structure comme ceci
        return [
            'migration' => $response['choices'][0]['message']['content']['migration'],
            'migration_name' => $response['choices'][0]['message']['content']['migration_name'],
            'model' => $response['choices'][0]['message']['content']['model'],
            'model_name' => $response['choices'][0]['message']['content']['model_name'],
            'controller' => $response['choices'][0]['message']['content']['controller'],
            'controller_name' => $response['choices'][0]['message']['content']['controller_name'],
            'route' => $response['choices'][0]['message']['content']['route'],
            'view' => $response['choices'][0]['message']['content']['view'],
            'view_name' => $response['choices'][0]['message']['content']['view_name'],
        ];
        
    }

    private function createMigrationFile($migrationContent, $migrationName)
    {
        $fileName = date('Y_m_d_His') . "_create_$migrationName.php"; // Utiliser le nom fourni par l'IA
        $migrationPath = database_path("migrations/$fileName");
        File::put($migrationPath, $migrationContent);
        return $fileName;
    }

    private function createModel($modelContent, $modelName)
    {
        $modelPath = app_path("Models/$modelName.php"); // Utiliser le nom fourni par l'IA
        File::put($modelPath, $modelContent);
    }

    private function createController($controllerContent, $controllerName)
    {
        $controllerPath = app_path("Http/Controllers/$controllerName.php"); // Utiliser le nom fourni par l'IA
        File::put($controllerPath, $controllerContent);
    }

    private function createRoute($routeContent)
    {
        $routePath = base_path('routes/web.php');
        File::append($routePath, $routeContent);
    }

    private function createView($viewContent, $viewName)
    {
        $viewPath = resource_path("views/$viewName.blade.php"); // Utiliser le nom fourni par l'IA
        File::put($viewPath, $viewContent);
    }
}
