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
        Schema::table('properties', function (Blueprint $table) {
            // Remove unit-level fields that should only be in units table
            $table->dropColumn([
                'rent_amount',
                'bedrooms', 
                'bathrooms',
                'size_sqft',
                'status'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // Add back the columns if rollback is needed
            $table->decimal('rent_amount', 10, 2)->nullable();
            $table->integer('bedrooms')->default(1);
            $table->integer('bathrooms')->default(1);
            $table->decimal('size_sqft', 8, 2)->nullable();
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
        });
    }
};
