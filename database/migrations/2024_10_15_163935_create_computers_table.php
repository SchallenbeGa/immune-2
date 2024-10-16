<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComputersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('computers', function (Blueprint $table) {
            $table->id();
            $table->string('garantie'); 
            $table->string('localisation'); 
            $table->string('date_achat'); 
            $table->string('date_fin_garantie'); 
            $table->string('reference');  // Référence de la machine
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade'); // Clé étrangère avec la table 'users'
            $table->timestamps();         // Ajoute created_at et updated_at
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('computers');
    }
}
