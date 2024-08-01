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
        Schema::create('students', function (Blueprint $table) {
            $table->id('stud_id'); // Primary key
            $table->string('stud_fname');
            $table->string('stud_lname');
            $table->string('stud_mname')->nullable();
            $table->string('stud_contact');
            $table->string('stud_email')->unique();
            $table->unsignedBigInteger('campus_id')->nullable(); // Make it nullable
            $table->unsignedBigInteger('college_id')->nullable(); // Make it nullable
            $table->unsignedBigInteger('program_id')->nullable(); // Make it nullable
            $table->unsignedBigInteger('major_id')->nullable(); // Make it nullable
            $table->unsignedBigInteger('year_id')->nullable(); // Make it nullable
            $table->string('enrollment_stat');
            $table->timestamps();

            $table->foreign('campus_id')->references('campus_id')->on('campuses')->onDelete('set null');
            $table->foreign('college_id')->references('college_id')->on('colleges')->onDelete('set null');
            $table->foreign('program_id')->references('program_id')->on('programs')->onDelete('set null');
            $table->foreign('major_id')->references('major_id')->on('majors')->onDelete('set null');
            $table->foreign('year_id')->references('year_id')->on('years')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
};
