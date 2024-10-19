<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeComputerHistoryTable extends Migration
{
    public function up()
    {
        Schema::create('employee_computer_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('computer_id');
            $table->unsignedBigInteger('employee_id');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('computer_id')->references('id')->on('computers')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_computer_history');
    }
}

