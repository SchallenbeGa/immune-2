<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrudOperationsTable extends Migration
{
    public function up()
    {
        Schema::create('crud_operations', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->string('migration_name');
            $table->string('model_name');
            $table->string('controller_name');
            $table->string('view_name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('crud_operations');
    }
}
