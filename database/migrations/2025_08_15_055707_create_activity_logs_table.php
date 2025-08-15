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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('activity_type'); // payment, maintenance, message, profile_update, etc.
            $table->string('description'); // Human readable description
            $table->json('metadata')->nullable(); // Additional data (amounts, IDs, etc.)
            $table->string('icon')->default('fas fa-circle'); // Icon for display
            $table->string('color')->default('blue'); // Color theme
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
