<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessageLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('recipient_type');
            $table->text('content');
            $table->string('schedule');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable(); // Place sent_at before status
            $table->string('status')->default('Pending'); // Default status as Pending
            $table->integer('total_recipients')->default(0); // Add total_recipients field
            $table->integer('sent_count')->default(0); // Add sent_count field
            $table->integer('failed_count')->default(0); // Add failed_count field
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message_logs');
    }
}
