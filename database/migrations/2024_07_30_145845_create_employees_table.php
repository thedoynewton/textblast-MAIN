<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id('emp_id');
            $table->string('emp_fname');
            $table->string('emp_lname');
            $table->string('emp_mname')->nullable();
            $table->string('emp_contact');
            $table->string('emp_email')->unique();
            $table->unsignedBigInteger('campus_id');
            $table->unsignedBigInteger('office_id');
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('type_id');
            $table->timestamps();

            $table->foreign('campus_id')->references('campus_id')->on('campuses')->onDelete('NO ACTION');
            $table->foreign('office_id')->references('office_id')->on('offices')->onDelete('NO ACTION');
            $table->foreign('status_id')->references('status_id')->on('statuses')->onDelete('NO ACTION');
            $table->foreign('type_id')->references('type_id')->on('types')->onDelete('NO ACTION');
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
