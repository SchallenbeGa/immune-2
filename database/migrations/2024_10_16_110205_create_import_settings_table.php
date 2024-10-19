<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('import_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('status');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('import_settings');
    }
}

