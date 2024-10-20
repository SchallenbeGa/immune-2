<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommandHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('command_histories', function (Blueprint $table) {
            $table->id();
            $table->string('command'); // Commande exécutée
            $table->text('output'); // Sortie de la commande
            $table->boolean('success'); // État de la commande (réussie ou échouée)
            $table->timestamps(); // Date de création et mise à jour
        });
    }

    public function down()
    {
        Schema::dropIfExists('command_histories');
    }
}
