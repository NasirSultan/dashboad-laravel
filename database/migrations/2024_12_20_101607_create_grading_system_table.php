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
        Schema::create('grading_system', function (Blueprint $table) {
            $table->id();
            $table->enum('grade', ['A', 'B', 'C', 'D', 'F']);
            $table->integer('min_days');
            $table->integer('max_days');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('grading_system');
    }
};
