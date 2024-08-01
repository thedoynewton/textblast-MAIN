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
        Schema::create('programs', function (Blueprint $table) {
            $table->id('program_id'); // Primary key
            $table->unsignedBigInteger('campus_id')->nullable(); // Make it nullable
            $table->unsignedBigInteger('college_id')->nullable(); // Make it nullable
            $table->string('program_name');
            $table->timestamps();

            $table->foreign('campus_id')->references('campus_id')->on('campuses')->onDelete('set null');
            $table->foreign('college_id')->references('college_id')->on('colleges')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('programs');
    }
};
