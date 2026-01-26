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
        Schema::create('complaint_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->enum('status', ['pending', 'ongoing', 'resolved'])->default('pending');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });

        Schema::create('complaint_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('sender_id');
            $table->text('message');
            $table->string('attachment_path', 2048)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_messages');
        Schema::dropIfExists('complaint_tickets');
    }
};
