<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('sites', function (Blueprint $table) {
        $table->id();
        $table->string('url');
        $table->string('name')->nullable();
        $table->string('screenshot_path')->nullable();
        $table->string('type')->default('http');
        $table->string('response')->nullable();
        $table->string('port')->nullable();
        $table->string('header')->nullable();
        $table->string('method')->default('get');
        $table->string('status')->default('200');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
