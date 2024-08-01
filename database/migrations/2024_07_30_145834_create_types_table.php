<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('types', function (Blueprint $table) {
            $table->id('type_id');
            $table->unsignedBigInteger('campus_id');
            $table->unsignedBigInteger('office_id');
            $table->unsignedBigInteger('status_id');
            $table->string('type_name');
            $table->timestamps();

            $table->foreign('campus_id')->references('campus_id')->on('campuses')->onDelete('NO ACTION');
            $table->foreign('office_id')->references('office_id')->on('offices')->onDelete('NO ACTION');
            $table->foreign('status_id')->references('status_id')->on('statuses')->onDelete('NO ACTION');
        });
    }

    public function down()
    {
        Schema::dropIfExists('types');
    }
};
