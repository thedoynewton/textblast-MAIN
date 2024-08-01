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
        Schema::create('years', function (Blueprint $table) {
            $table->id('year_id'); // Primary key
            $table->string('year_name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('years');
    }
};
