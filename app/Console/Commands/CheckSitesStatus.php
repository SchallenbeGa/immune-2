<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Site;
use App\Http\Controllers\SiteController;

class CheckSitesStatus extends Command
{
    protected $signature = 'sites:check-status';
    protected $description = 'Vérifie le statut des sites et enregistre l\'historique';

    public function handle()
    {
        $sites = Site::all();
        $controller = new SiteController();

        foreach ($sites as $site) {
            // $controller->checkSite($site);
            $controller->CheckServices($site);
        }

        $this->info('Vérification du statut des sites terminée.');
    }
    
}
