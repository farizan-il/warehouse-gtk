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
        if (!Schema::hasTable('complaint_messages')) {
            Schema::create('complaint_messages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ticket_id');
                $table->unsignedBigInteger('sender_id');
                $table->text('message');
                $table->string('attachment_path', 2048)->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_messages');
    }
};
