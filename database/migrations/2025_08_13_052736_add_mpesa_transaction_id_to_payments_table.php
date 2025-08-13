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
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('mpesa_transaction_id')->nullable()->after('notes');
            $table->foreign('mpesa_transaction_id')->references('id')->on('mpesa_transactions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['mpesa_transaction_id']);
            $table->dropColumn('mpesa_transaction_id');
        });
    }
};
