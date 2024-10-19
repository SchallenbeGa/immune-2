<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileRecommandationsTable extends Migration
{
    public function up()
    {
        Schema::create('file_recommandations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_file_id');
            $table->string('file_path');
            $table->text('action_performed');
            $table->text('recommendation');
            $table->timestamps();

            $table->foreign('project_file_id')->references('id')->on('project_files')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('file_recommandations');
    }
}
