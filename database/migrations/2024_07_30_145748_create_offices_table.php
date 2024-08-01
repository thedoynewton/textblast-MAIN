<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->id('office_id');
            $table->unsignedBigInteger('campus_id');
            $table->string('office_name');
            $table->timestamps();

            $table->foreign('campus_id')->references('campus_id')->on('campuses')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('offices');
    }
};
