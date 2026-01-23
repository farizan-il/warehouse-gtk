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
        Schema::create('system_errors', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->string('type')->index(); // e.g., 'PHP Exception', 'Database Error', 'API Error'
            $table->string('file')->nullable();
            $table->integer('line')->nullable();
            $table->string('status')->default('pending'); // pending, resolved, ignored
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_errors');
    }
};
