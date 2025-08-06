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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->string('subject');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->string('priority')->default('normal'); // normal, high, urgent
            $table->foreignId('property_id')->nullable()->constrained('properties')->onDelete('set null');
            $table->string('message_type')->default('general'); // general, maintenance, lease, payment
            $table->json('attachments')->nullable();
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['sender_id', 'created_at']);
            $table->index(['receiver_id', 'is_read']);
            $table->index(['property_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
