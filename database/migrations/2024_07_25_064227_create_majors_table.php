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
        Schema::create('majors', function (Blueprint $table) {
            $table->id('major_id'); // Primary key
            $table->unsignedBigInteger('campus_id')->nullable(); // Make it nullable
            $table->unsignedBigInteger('college_id')->nullable(); // Make it nullable
            $table->unsignedBigInteger('program_id')->nullable(); // Make it nullable
            $table->string('major_name');
            $table->timestamps();

            $table->foreign('campus_id')->references('campus_id')->on('campuses')->onDelete('set null');
            $table->foreign('college_id')->references('college_id')->on('colleges')->onDelete('set null');
            $table->foreign('program_id')->references('program_id')->on('programs')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('majors');
    }
};
