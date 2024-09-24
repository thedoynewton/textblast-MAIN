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
        Schema::create('message_recipients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('message_log_id'); // Foreign key to message_logs table
            $table->enum('recipient_type', ['student', 'employee']);
            $table->unsignedBigInteger('stud_id')->nullable(); // Student ID if recipient is a student
            $table->unsignedBigInteger('emp_id')->nullable();  // Employee ID if recipient is an employee
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('campus_id')->nullable();
            $table->unsignedBigInteger('college_id')->nullable(); // For students
            $table->unsignedBigInteger('program_id')->nullable(); // For students
            $table->unsignedBigInteger('major_id')->nullable();   // For students
            $table->unsignedBigInteger('year_id')->nullable();    // For students
            $table->string('enrollment_stat')->nullable();        // For students
            $table->unsignedBigInteger('office_id')->nullable();  // For employees
            $table->unsignedBigInteger('status_id')->nullable();  // For employees
            $table->unsignedBigInteger('type_id')->nullable();    // For employees
            $table->enum('sent_status', ['Sent', 'Failed'])->default('Failed'); // Status of the message
            $table->timestamps();

            // Foreign keys
            $table->foreign('message_log_id')->references('id')->on('message_logs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_recipients');
    }
};
