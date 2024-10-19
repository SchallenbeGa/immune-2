<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportSettingsRowTable extends Migration
{
    public function up()
    {
        Schema::create('import_settings_row', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('import_settings_id');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();
            $table->foreign('import_settings_id')->references('id')->on('import_settings')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('import_settings_row');
    }
}

