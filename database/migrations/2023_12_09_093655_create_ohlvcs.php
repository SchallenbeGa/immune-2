<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ohlvcs', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->foreignId('symbol_id')->constrained('symbols')->onDelete('cascade');
            $table->string('open');
            $table->string('high');
            $table->string('low');
            $table->string('volume');
            $table->string('close');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ohlvc');
    }
};
