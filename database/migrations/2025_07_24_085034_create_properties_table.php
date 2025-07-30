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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landlord_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('location');
            $table->string('address');
            $table->decimal('rent_amount', 10, 2);
            $table->integer('bedrooms')->default(1);
            $table->integer('bathrooms')->default(1);
            $table->decimal('size_sqft', 8, 2)->nullable();
            $table->string('property_type')->default('apartment'); // apartment, house, studio, etc.
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
            $table->string('image')->nullable();
            $table->json('amenities')->nullable(); // JSON array of amenities
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
