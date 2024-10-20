<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address');       // Adresse IP du visiteur
            $table->string('url');              // URL visitée
            $table->string('http_method');      // Méthode HTTP
            $table->text('user_agent');         // Navigateur et OS
            $table->string('referer')->nullable(); // Référent (site d'origine)
            $table->string('country')->nullable();
            $table->timestamps();               // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visits');
    }
}
