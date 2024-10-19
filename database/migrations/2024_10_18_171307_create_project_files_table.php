<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectFilesTable extends Migration
{
    public function up()
    {
        Schema::create('project_files', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');          // Nom du fichier
            $table->string('file_path');          // Chemin complet du fichier
            $table->integer('file_size')->nullable();   // Taille du fichier en octets
            $table->timestamp('last_modified')->nullable(); // Date de la dernière modification
            $table->timestamps();  // Pour 'created_at' et 'updated_at'

        });
    }

    public function down()
    {
        Schema::dropIfExists('project_files');
    }
}
